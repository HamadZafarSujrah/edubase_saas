<?php

namespace App\Services;

use App\Models\Finance\Challan;
use App\Models\Finance\ChallanDiscount;
use App\Models\Finance\ChallanItem;
use App\Models\Finance\GLAccount;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalItem;
use App\Models\Student\Student;
use App\Models\Finance\StudentFeePlanItem;
use Illuminate\Support\Facades\DB;

class FeeService
{
    /**
     * Generates a monthly fee challan for a given student based on their personalized fee plan.
     *
     * @param Student $student
     * @param string $month
     * @param string|int $year
     * @param string $dueDate
     * @return Challan|null
     * @throws \Exception
     */
    public function generateMonthlyChallan(Student $student, $month, $year, $dueDate)
    {
        // Check if challan already exists for this month/year/student
        $exists = Challan::where([
            'student_id' => $student->id, 
            'month' => $month, 
            'year' => $year
        ])->exists();
        
        if ($exists) return null;

        // Get INDIVIDUALIZED items for this student
        $personalParticulars = StudentFeePlanItem::with('particular')
            ->where('student_id', $student->id)
            ->get();

        if ($personalParticulars->isEmpty()) return null;

        DB::beginTransaction();
        try {
            // Calculate net total (Actual - Discount)
            $total = 0;
            foreach ($personalParticulars as $pp) {
                $total += ($pp->actual_amount - $pp->discount_amount);
            }

            // Create Challan Header. The exists() check above is a plain
            // check-then-insert and races under concurrent/duplicate requests --
            // the unique (tenant_id, student_id, month, year) index on challans
            // is the real guard, enforced atomically by the DB. A collision here
            // (on that index, or on challan_no's much larger random tail) means
            // another request already created this student's challan for this
            // month/year, so treat it as idempotent rather than a fatal error.
            try {
                $challan = Challan::create([
                    'tenant_id' => $student->tenant_id,
                    'student_id' => $student->id,
                    'challan_no' => 'CHL-' . strtoupper($month) . '-' . $year . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999),
                    'month' => $month,
                    'year' => $year,
                    'issue_date' => date('Y-m-d'),
                    'due_date' => $dueDate,
                    'total_amount' => $total,
                    'status' => 'pending'
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                return null;
            }

            // Create Items using customized amounts
            foreach ($personalParticulars as $pp) {
                $netAmount = $pp->actual_amount - $pp->discount_amount;
                if ($netAmount <= 0) continue; // Skip if fully discounted

                ChallanItem::create([
                    'challan_id' => $challan->id,
                    'fee_particular_id' => $pp->fee_particular_id,
                    'particular_name' => $pp->particular->name,
                    'amount' => $netAmount
                ]);
            }
            
            DB::commit();
            return $challan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Splits $student's monthly bill into $installments separate challans instead of
     * one, each due $intervalDays apart starting at $firstDueDate. Each fee particular's
     * net amount is divided evenly across installments, with any rounding remainder
     * absorbed by the last installment so the sum always equals the particular's
     * original net amount exactly.
     *
     * Shares the same (tenant_id, student_id, month, year) duplicate guard as
     * generateMonthlyChallan() -- if any challan already exists for this
     * student/month/year (regular or installment), nothing is generated. The
     * installment_no column (added specifically for this feature) lets multiple
     * challan rows coexist for the same student/month/year.
     *
     * @return \App\Models\Finance\Challan[]
     */
    public function generateInstallmentChallans(Student $student, $month, $year, int $installments, string $firstDueDate, int $intervalDays)
    {
        $exists = Challan::where([
            'student_id' => $student->id,
            'month' => $month,
            'year' => $year,
        ])->exists();

        if ($exists) return [];

        $personalParticulars = StudentFeePlanItem::with('particular')
            ->where('student_id', $student->id)
            ->get();

        if ($personalParticulars->isEmpty()) return [];

        // [fee_particular_id, name, splits: [installment amount, ...]]
        $itemSplits = [];
        foreach ($personalParticulars as $pp) {
            $net = $pp->actual_amount - $pp->discount_amount;
            if ($net <= 0) continue;

            $itemSplits[] = [
                'fee_particular_id' => $pp->fee_particular_id,
                'name' => $pp->particular->name,
                'splits' => $this->splitAmount((float) $net, $installments),
            ];
        }

        if (empty($itemSplits)) return [];

        $challans = [];

        DB::beginTransaction();
        try {
            $dueDate = \Carbon\Carbon::parse($firstDueDate);

            for ($n = 1; $n <= $installments; $n++) {
                $index = $n - 1;
                $installmentTotal = round(array_sum(array_map(fn ($i) => $i['splits'][$index], $itemSplits)), 2);

                if ($installmentTotal <= 0) continue;

                try {
                    $challan = Challan::create([
                        'tenant_id' => $student->tenant_id,
                        'student_id' => $student->id,
                        'challan_no' => 'CHL-' . strtoupper($month) . '-' . $year . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999) . '-I' . $n,
                        'month' => $month,
                        'year' => $year,
                        'installment_no' => $n,
                        'issue_date' => date('Y-m-d'),
                        'due_date' => $dueDate->copy()->addDays(($n - 1) * $intervalDays)->format('Y-m-d'),
                        'total_amount' => $installmentTotal,
                        'status' => 'pending',
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    DB::rollBack();
                    return [];
                }

                foreach ($itemSplits as $item) {
                    $share = $item['splits'][$index];
                    if ($share <= 0) continue;

                    ChallanItem::create([
                        'challan_id' => $challan->id,
                        'fee_particular_id' => $item['fee_particular_id'],
                        'particular_name' => $item['name'] . " (Installment {$n}/{$installments})",
                        'amount' => $share,
                    ]);
                }

                $challans[] = $challan;
            }

            DB::commit();
            return $challans;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Divides $amount into $n parts that sum to exactly $amount, with any rounding
     * remainder absorbed by the last part (so no fraction of a cent is ever lost).
     * Public so other callers needing the same proportional-split guarantee (e.g.
     * Family-Wise Payment allocating a challan's remaining balance across its
     * original items) can reuse it instead of re-deriving the rounding logic.
     */
    public function splitAmount(float $amount, int $n): array
    {
        $base = floor(($amount / $n) * 100) / 100;
        $parts = array_fill(0, $n, $base);
        $parts[$n - 1] = round($amount - $base * ($n - 1), 2);

        return $parts;
    }

    /**
     * Allocates $amount across $weights proportionally (e.g. a challan's remaining
     * balance split across its original items by each item's own amount), with any
     * rounding remainder absorbed by the last weight so shares always sum to exactly
     * $amount even when $weights themselves don't sum to $amount.
     */
    public function splitProportional(float $amount, array $weights): array
    {
        $weightTotal = array_sum($weights);
        if ($weightTotal <= 0) {
            return array_fill(0, count($weights), 0.0);
        }

        $shares = [];
        $runningTotal = 0.0;
        $count = count($weights);

        foreach ($weights as $i => $weight) {
            if ($i === $count - 1) {
                $shares[] = round($amount - $runningTotal, 2);
                break;
            }
            $share = round($amount * ($weight / $weightTotal), 2);
            $shares[] = $share;
            $runningTotal += $share;
        }

        return $shares;
    }

    /**
     * Records a payment against a single challan: updates challan totals, posts the
     * balanced GL journal entry, and persists every discount (per-line and named/
     * catalogued) as a ChallanDiscount row so it stays reportable by type.
     *
     * Kept here rather than inline in a Livewire component so Family-Wise Payment,
     * Direct Payment, alumni challan payment, hostel fee collection (which reuses the
     * challans table per the ER diagram), and the online-payment webhook handlers can
     * all post through the same accounting logic instead of re-implementing it.
     *
     * Expected $input shape:
     *   tenant_id, receiving_account_id, discount_account_id (nullable), paid_date,
     *   receipt_no (nullable), challan_notes (nullable), paid_by (user id),
     *   line_items: [['name', 'current_payment', 'discount'], ...],
     *   named_discounts: [['discount_type_id', 'amount', 'reason'], ...],
     *   total_paid, total_discount, total_after_discount
     */
    public function collectChallanPayment(Challan $challan, array $input): JournalEntry
    {
        return DB::transaction(function () use ($challan, $input) {
            // Re-fetch with a row lock: two concurrent payment submissions on the
            // same challan must serialize on this row instead of both reading the
            // same stale paid_amount/discount_amount and racing on the update below.
            $challan = Challan::whereKey($challan->id)->lockForUpdate()->first();
            $student    = $challan->student;
            $tenantId   = $input['tenant_id'];

            // This visit's amounts (used for the journal entry -- each visit is
            // its own distinct transaction) vs. the challan's running totals
            // (which must accumulate across visits, not be overwritten by them).
            $visitPaid     = (float) $input['total_paid'];
            $visitDiscount = (float) $input['total_discount'];
            $lineItems       = $input['line_items'] ?? [];
            $namedDiscounts  = $input['named_discounts'] ?? [];
            $namedDiscountTotal = array_sum(array_map(fn($d) => (float) ($d['amount'] ?: 0), $namedDiscounts));

            $priorPaid     = (float) $challan->paid_amount;
            $priorDiscount = (float) $challan->discount_amount;
            $newPaid       = $priorPaid + $visitPaid;
            $newDiscount   = $priorDiscount + $visitDiscount;
            $totalAmount   = (float) $challan->total_amount;

            // A challan's total_amount never changes after generation -- refuse
            // to record a payment that would push cumulative paid+discount past
            // it, instead of silently allowing an overpayment.
            if (round($newPaid + $newDiscount - $totalAmount, 2) > 0.01) {
                $remaining = max(0, $totalAmount - $priorPaid - $priorDiscount);
                throw new \RuntimeException(
                    'This payment exceeds the remaining balance on this challan (remaining: ' .
                    number_format($remaining, 2) . ').'
                );
            }

            $receiptNo = $input['receipt_no'] ?: (
                'RCPT-' . strtoupper($challan->month) . '-' . $challan->year . '-' . $challan->id .
                '-' . now()->format('His') . rand(100, 999)
            );

            $challan->update([
                'status'               => (round($newPaid + $newDiscount - $totalAmount, 2) >= -0.01) ? 'paid' : 'partial',
                'paid_amount'          => $newPaid,
                'discount_amount'      => $newDiscount,
                'receiving_account_id' => $input['receiving_account_id'],
                'discount_account_id'  => $input['discount_account_id'] ?: null,
                'paid_date'            => $input['paid_date'],
                'due_date'             => $input['due_date'] ?? $challan->due_date,
                'receipt_no'           => $receiptNo,
                'challan_notes'        => $input['challan_notes'] ?? null,
                'paid_by'              => $input['paid_by'],
            ]);

            $entry = JournalEntry::create([
                'tenant_id'        => $tenantId,
                'campus_id'        => $student->campus_id,
                'transaction_date' => $input['paid_date'],
                'voucher_no'       => $receiptNo,
                'narration'        => "Fee Received: {$student->first_name} {$student->last_name} | {$challan->challan_no}" . (!empty($input['challan_notes']) ? " | {$input['challan_notes']}" : ''),
                'created_by'       => $input['paid_by'],
            ]);

            if ($visitPaid > 0) {
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id'    => $input['receiving_account_id'],
                    'debit'            => $visitPaid,
                    'credit'           => 0,
                    'item_memo'        => "Fee received from {$student->admission_no}",
                ]);
            }

            if ($visitDiscount > 0 && !empty($input['discount_account_id'])) {
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id'    => $input['discount_account_id'],
                    'debit'            => $visitDiscount,
                    'credit'           => 0,
                    'item_memo'        => "Discount/Write-off for {$student->admission_no}",
                ]);
            }

            foreach ($lineItems as $item) {
                $currentPayment = $item['current_payment'] === '' ? 0 : (float) $item['current_payment'];
                $lineDiscount   = $item['discount'] === '' ? 0 : (float) $item['discount'];
                $itemTotal      = $currentPayment + $lineDiscount;

                if ($itemTotal > 0) {
                    $incomeAccount = GLAccount::where('tenant_id', $tenantId)
                        ->where('name', $item['name'])
                        ->first();

                    if (!$incomeAccount) {
                        $incomeAccount = GLAccount::where('tenant_id', $tenantId)
                            ->where(function ($q) {
                                $q->where('name', 'like', '%Receivable%')
                                  ->orWhere('name', 'like', '%Revenue%');
                            })->first();
                    }

                    if (!$incomeAccount) {
                        throw new \RuntimeException(
                            "No income GL account found for '{$item['name']}' (and no fallback Receivable/Revenue " .
                            "account exists). Fix the chart of accounts before collecting this payment -- posting " .
                            "would otherwise leave the journal entry unbalanced."
                        );
                    }

                    JournalItem::create([
                        'journal_entry_id' => $entry->id,
                        'gl_account_id'    => $incomeAccount->id,
                        'debit'            => 0,
                        'credit'           => $itemTotal,
                        'item_memo'        => "{$item['name']} for {$student->admission_no}",
                    ]);
                }

                // Recorded so per-line discounts show up in challan_discounts alongside
                // named ones — otherwise any future report/dashboard grouping by
                // discount type would miss every ad-hoc line discount.
                if ($lineDiscount > 0) {
                    ChallanDiscount::create([
                        'challan_id'       => $challan->id,
                        'discount_type_id' => null,
                        'amount'           => $lineDiscount,
                        'reason'           => "Line discount: {$item['name']}",
                    ]);
                }
            }

            if ($namedDiscountTotal > 0) {
                $incomeAccount = GLAccount::where('tenant_id', $tenantId)
                    ->where(function ($q) {
                        $q->where('name', 'like', '%Receivable%')
                          ->orWhere('name', 'like', '%Revenue%');
                    })->first();

                if (!$incomeAccount) {
                    throw new \RuntimeException(
                        'No Receivable/Revenue GL account found to recognize named discounts against. ' .
                        'Fix the chart of accounts before collecting this payment.'
                    );
                }

                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id'    => $incomeAccount->id,
                    'debit'            => 0,
                    'credit'           => $namedDiscountTotal,
                    'item_memo'        => "Named discounts applied for {$student->admission_no}",
                ]);
            }

            foreach ($namedDiscounts as $discount) {
                $amount = (float) ($discount['amount'] ?: 0);
                if ($amount <= 0) {
                    continue;
                }

                ChallanDiscount::create([
                    'challan_id'       => $challan->id,
                    'discount_type_id' => $discount['discount_type_id'] ?: null,
                    'amount'           => $amount,
                    'reason'           => $discount['reason'] ?: null,
                ]);
            }

            return $entry;
        });
    }
}
