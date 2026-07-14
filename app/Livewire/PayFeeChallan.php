<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\Challan;
use App\Models\Finance\ChallanItem;
use App\Models\Finance\GLAccount;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PayFeeChallan extends Component
{
    public $challan_id;
    public $challan;
    public $student;

    // Payment fields
    public $receipt_no      = '';
    public $due_date        = '';
    public $paid_date       = '';
    public $challan_notes   = '';

    // Account selections
    public $receiving_account_id  = '';
    public $discount_account_id   = '';

    // Line items with per-row discount support
    public $line_items = [];

    // Computed totals (updated reactively)
    public $total_payable       = 0;
    public $total_discount      = 0;
    public $total_after_discount = 0;
    public $total_paid          = 0;
    public $total_remaining     = 0;

    // Other challans of this student
    public $other_challans = [];

    public function mount()
    {
        $this->challan_id = request()->query('challan_id');

        if (!$this->challan_id) {
            abort(400, 'Challan ID is required.');
        }
        $this->challan  = Challan::with(['student.campus', 'student.schoolClass', 'student.section', 'student.session', 'items'])->findOrFail($this->challan_id);
        $this->student  = $this->challan->student;

        $this->due_date  = $this->challan->due_date ?? '';
        $this->paid_date = date('Y-m-d');

        // Build line items from challan items
        $this->line_items = [];
        foreach ($this->challan->items as $item) {
            $this->line_items[] = [
                'id'               => $item->id,
                'name'             => $item->particular_name,
                'payable'          => (float)$item->amount,
                'discount'         => 0.0,
                'after_discount'   => (float)$item->amount,
                'current_payment'  => '', // Start empty
                'remaining'        => (float)$item->amount,
            ];
        }

        $this->recalculateTotals();

        // Auto-select default receiving account (user's own cashier account)
        $cashier = GLAccount::where('user_id', Auth::id())->first();
        if ($cashier) {
            $this->receiving_account_id = $cashier->id;
        }

        // Other pending challans of same student
        $this->other_challans = Challan::where('student_id', $this->student->id)
            ->where('id', '!=', $this->challan_id)
            ->where('status', '!=', 'paid')
            ->get(['id', 'challan_no', 'month', 'year', 'total_amount', 'due_date', 'status'])
            ->toArray();
    }

    public function updatedLineItems($value, $key)
    {
        [$index, $field] = explode('.', $key, 2);
        $index = (int)$index;

        if (in_array($field, ['discount', 'current_payment'])) {
            $payable  = (float)($this->line_items[$index]['payable'] ?? 0);
            $discount = (float)($this->line_items[$index]['discount'] ?? 0);
            $afterDiscount = max(0, $payable - $discount);
            
            $currentPaymentInput = $this->line_items[$index]['current_payment'];
            $currentPayment = $currentPaymentInput === '' ? 0 : (float)$currentPaymentInput;
            
            // Prevent paying more than what's after discount
            if ($currentPayment > $afterDiscount) {
                $currentPayment = $afterDiscount;
                $this->line_items[$index]['current_payment'] = $currentPayment;
            }

            $this->line_items[$index]['after_discount']  = $afterDiscount;
            $this->line_items[$index]['remaining']       = max(0, $afterDiscount - $currentPayment);
        }

        $this->recalculateTotals();
    }

    protected function recalculateTotals()
    {
        $this->total_payable        = array_sum(array_column($this->line_items, 'payable'));
        $this->total_discount       = array_sum(array_column($this->line_items, 'discount'));
        $this->total_after_discount = array_sum(array_column($this->line_items, 'after_discount'));
        
        $paid = 0;
        foreach ($this->line_items as $item) {
            $val = $item['current_payment'] ?? 0;
            $paid += ($val === '') ? 0 : (float)$val;
        }
        $this->total_paid = $paid;
        
        $this->total_remaining      = $this->total_after_discount - $this->total_paid;
    }

    public function submit()
    {
        $this->validate([
            'receiving_account_id' => 'required|exists:gl_accounts,id',
            'paid_date'            => 'required|date',
        ], [
            'receiving_account_id.required' => 'Please select a Payment Receiving Account.',
        ]);

        // If there is any discount, require a discount account
        if ($this->total_discount > 0 && !$this->discount_account_id) {
            $this->addError('discount_account_id', 'Please select a Discount / Bad Debt Account since you have entered a discount.');
            return;
        }

        DB::transaction(function () {
            $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
            $paidAmount     = $this->total_paid;
            $discountAmount = $this->total_discount;
            $receiptNo      = $this->receipt_no ?: ('RCPT-' . strtoupper($this->challan->month) . '-' . $this->challan->year . '-' . $this->challan->id);

            // Update challan
            $this->challan->update([
                'status'               => ($paidAmount >= $this->total_after_discount) ? 'paid' : 'partial',
                'paid_amount'          => $paidAmount,
                'discount_amount'      => $discountAmount,
                'receiving_account_id' => $this->receiving_account_id,
                'discount_account_id'  => $this->discount_account_id ?: null,
                'paid_date'            => $this->paid_date,
                'receipt_no'           => $receiptNo,
                'challan_notes'        => $this->challan_notes,
                'paid_by'              => Auth::id(),
            ]);

            // Create Journal Entry
            $entry = JournalEntry::create([
                'tenant_id'        => $tenantId,
                'campus_id'        => $this->student->campus_id,
                'transaction_date' => $this->paid_date,
                'voucher_no'       => $receiptNo,
                'narration'        => "Fee Received: {$this->student->first_name} {$this->student->last_name} | {$this->challan->challan_no}" . ($this->challan_notes ? " | {$this->challan_notes}" : ''),
                'created_by'       => Auth::id(),
            ]);

            // Debit: Receiving Account (Cash/Bank)
            if ($paidAmount > 0) {
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id'    => $this->receiving_account_id,
                    'debit'            => $paidAmount,
                    'credit'           => 0,
                    'item_memo'        => "Fee received from {$this->student->admission_no}",
                ]);
            }

            // Debit: Discount/Bad Debt Account
            if ($discountAmount > 0 && $this->discount_account_id) {
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id'    => $this->discount_account_id,
                    'debit'            => $discountAmount,
                    'credit'           => 0,
                    'item_memo'        => "Discount/Write-off for {$this->student->admission_no}",
                ]);
            }

            // Credit: Specific Income Accounts based on Fee Particulars
            foreach ($this->line_items as $item) {
                $currentPayment = $item['current_payment'] === '' ? 0 : (float)$item['current_payment'];
                $discountAmount = $item['discount'] === '' ? 0 : (float)$item['discount'];
                $itemTotal = $currentPayment + $discountAmount;
                
                if ($itemTotal > 0) {
                    // Try to find exact income account matching the fee particular
                    $incomeAccount = GLAccount::where('tenant_id', $tenantId)
                        ->where('name', $item['name'])
                        ->first();
                        
                    // Fallback to Fee Receivable or any Revenue account
                    if (!$incomeAccount) {
                        $incomeAccount = GLAccount::where('tenant_id', $tenantId)
                            ->where(function($q) {
                                $q->where('name', 'like', '%Receivable%')
                                  ->orWhere('name', 'like', '%Revenue%');
                            })->first();
                    }

                    if ($incomeAccount) {
                        JournalItem::create([
                            'journal_entry_id' => $entry->id,
                            'gl_account_id'    => $incomeAccount->id,
                            'debit'            => 0,
                            'credit'           => $itemTotal,
                            'item_memo'        => "{$item['name']} for {$this->student->admission_no}",
                        ]);
                    }
                }
            }
        });

        session()->flash('success', 'Payment recorded successfully!');
        return redirect()->route('finance.pay-print-challans');
    }

    public function render()
    {
        return view('livewire.pay-fee-challan', [
            'receiving_accounts' => GLAccount::where('tenant_id', session('tenant_id') ?? Auth::user()->tenant_id)
                ->where('is_inactive', 0)
                ->whereIn('type', ['income', 'asset'])
                ->get(),
            'discount_accounts'  => GLAccount::where('tenant_id', session('tenant_id') ?? Auth::user()->tenant_id)
                ->where('is_inactive', 0)
                ->where('type', 'expense')
                ->get(),
        ])->layout('layouts.app');
    }
}
