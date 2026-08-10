<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Finance\Challan;
use App\Models\Finance\ChallanItem;
use App\Models\Finance\GLAccount;
use App\Services\FeeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DirectPayment extends Component
{
    public $student_search = '';
    public $suggested_students = [];
    public $student_id = '';
    public $selected_student = null;

    // [['name' => ..., 'amount' => ..., 'discount' => ...]]
    public $line_items = [];

    public $receiving_account_id = '';
    public $discount_account_id = '';
    public $paid_date;
    public $receipt_no = '';
    public $challan_notes = '';

    public function mount()
    {
        $this->paid_date = date('Y-m-d');
        $this->line_items = [['name' => '', 'amount' => '', 'discount' => '']];
    }

    public function updatedStudentSearch()
    {
        if (strlen($this->student_search) < 2) {
            $this->suggested_students = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->suggested_students = Student::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->student_search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->student_search . '%')
                  ->orWhere('father_name', 'like', '%' . $this->student_search . '%');
            })
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'admission_no', 'father_name'])
            ->toArray();
    }

    public function selectStudent($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->selected_student = Student::where('tenant_id', $tenantId)->find($id);
        $this->student_id = $id;
        $this->suggested_students = [];
        $this->student_search = trim(($this->selected_student->first_name ?? '') . ' ' . ($this->selected_student->last_name ?? ''));
    }

    public function addLineItem() { $this->line_items[] = ['name' => '', 'amount' => '', 'discount' => '']; }

    public function removeLineItem($index)
    {
        unset($this->line_items[$index]);
        $this->line_items = array_values($this->line_items);
    }

    public function getTotalPayableProperty()
    {
        return array_sum(array_map(fn ($r) => (float) ($r['amount'] ?: 0), $this->line_items));
    }

    public function getTotalDiscountProperty()
    {
        return array_sum(array_map(fn ($r) => (float) ($r['discount'] ?: 0), $this->line_items));
    }

    public function getTotalPaidProperty()
    {
        return $this->totalPayable - $this->totalDiscount;
    }

    public function submit()
    {
        $this->validate([
            'student_id' => 'required|exists:students,id',
            'receiving_account_id' => 'required|exists:gl_accounts,id',
            'paid_date' => 'required|date',
        ]);

        $validLines = array_values(array_filter($this->line_items, fn ($r) => $r['name'] && $r['amount'] !== ''));
        if (empty($validLines)) {
            $this->addError('line_items', 'Enter at least one particular with an amount.');
            return;
        }

        foreach ($validLines as $row) {
            if ((float) ($row['discount'] ?: 0) > (float) $row['amount']) {
                $this->addError('line_items', 'A line\'s discount cannot exceed its amount.');
                return;
            }
        }

        if ($this->totalDiscount > 0 && !$this->discount_account_id) {
            $this->addError('discount_account_id', 'Please select a Discount / Bad Debt Account since you have entered a discount.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        try {
            DB::transaction(function () use ($tenantId, $validLines) {
                $challan = Challan::create([
                    'tenant_id' => $tenantId,
                    'student_id' => $this->student_id,
                    'challan_no' => 'DPR-' . date('Ymd') . '-' . str_pad($this->student_id, 5, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999),
                    'month' => 'direct-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                    'year' => date('Y'),
                    'installment_no' => 1,
                    'issue_date' => date('Y-m-d'),
                    'due_date' => $this->paid_date,
                    'total_amount' => $this->totalPayable,
                    'status' => 'pending',
                ]);

                foreach ($validLines as $row) {
                    ChallanItem::create([
                        'challan_id' => $challan->id,
                        'fee_particular_id' => null,
                        'particular_name' => $row['name'],
                        'amount' => $row['amount'],
                    ]);
                }

                $lineItemsInput = array_map(fn ($r) => [
                    'name' => $r['name'],
                    'current_payment' => (float) $r['amount'] - (float) ($r['discount'] ?: 0),
                    'discount' => (float) ($r['discount'] ?: 0),
                ], $validLines);

                app(FeeService::class)->collectChallanPayment($challan, [
                    'tenant_id' => $tenantId,
                    'receiving_account_id' => $this->receiving_account_id,
                    'discount_account_id' => $this->discount_account_id,
                    'paid_date' => $this->paid_date,
                    'due_date' => $this->paid_date,
                    'receipt_no' => $this->receipt_no,
                    'challan_notes' => $this->challan_notes,
                    'paid_by' => Auth::id(),
                    'line_items' => $lineItemsInput,
                    'named_discounts' => [],
                    'total_paid' => $this->totalPaid,
                    'total_discount' => $this->totalDiscount,
                    'total_after_discount' => $this->totalPaid,
                ]);
            });
        } catch (\RuntimeException $e) {
            $this->addError('line_items', $e->getMessage());
            return;
        }

        session()->flash('message', 'Direct payment recorded successfully.');
        $this->reset(['student_search', 'suggested_students', 'student_id', 'selected_student', 'receipt_no', 'challan_notes']);
        $this->line_items = [['name' => '', 'amount' => '', 'discount' => '']];
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.direct-payment', [
            'receiving_accounts' => GLAccount::where('tenant_id', $tenantId)
                ->where('is_inactive', 0)
                ->whereIn('type', ['income', 'asset'])
                ->get(),
            'discount_accounts' => GLAccount::where('tenant_id', $tenantId)
                ->where('is_inactive', 0)
                ->where('type', 'expense')
                ->get(),
        ])->layout('layouts.app');
    }
}
