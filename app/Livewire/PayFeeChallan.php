<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\Challan;
use App\Models\Finance\DiscountType;
use App\Models\Finance\GLAccount;
use App\Services\FeeService;
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

    // Named/catalogued discounts (sibling, merit, scholarship, ...) applied at the challan level
    public $named_discounts = [];

    // Discounts already persisted against this challan from a previous visit (read-only)
    public $existing_discounts = [];

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

        // Discounts already applied to this challan on a previous visit (read-only)
        $this->existing_discounts = $this->challan->discounts()
            ->with('discountType')
            ->get()
            ->toArray();

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

            // Clamp the raw discount itself (not just its display) to this
            // line's own payable amount -- otherwise an over-entered discount
            // still flows unchanged into recalculateTotals() and gets persisted
            // as a ChallanDiscount write-off bigger than the fee ever was.
            if ($discount > $payable) {
                $discount = $payable;
                $this->line_items[$index]['discount'] = $discount;
            }

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

    public function addNamedDiscount()
    {
        $this->named_discounts[] = [
            'discount_type_id' => '',
            'percent'          => '',
            'amount'           => 0.0,
            'reason'           => '',
        ];
    }

    public function removeNamedDiscount($index)
    {
        unset($this->named_discounts[$index]);
        $this->named_discounts = array_values($this->named_discounts);
        $this->recalculateTotals();
    }

    public function updatedNamedDiscounts($value, $key)
    {
        [$index, $field] = explode('.', $key, 2);
        $index = (int) $index;

        // For a percentage-type discount, recompute the amount from the entered
        // percent against total_payable (the pre-discount fee total) whenever the
        // chosen type or the percent value changes.
        if (in_array($field, ['discount_type_id', 'percent'])) {
            $discountTypeId = $this->named_discounts[$index]['discount_type_id'] ?? null;
            $discountType   = $discountTypeId ? DiscountType::find($discountTypeId) : null;

            if ($discountType && $discountType->type === 'percent') {
                $percent = (float) ($this->named_discounts[$index]['percent'] ?: 0);
                $this->named_discounts[$index]['amount'] = round($this->total_payable * $percent / 100, 2);
            }
        }

        $this->recalculateTotals();
    }

    protected function recalculateTotals()
    {
        $this->total_payable        = array_sum(array_column($this->line_items, 'payable'));
        $lineItemDiscount           = array_sum(array_column($this->line_items, 'discount'));
        $namedDiscount              = array_sum(array_map(fn($d) => (float) ($d['amount'] ?: 0), $this->named_discounts));
        $this->total_discount       = $lineItemDiscount + $namedDiscount;
        $this->total_after_discount = $this->total_payable - $this->total_discount;

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

        // Per-line current-payment inputs are only clamped against that line's own
        // discount, not the named/challan-level discounts added above — so a named
        // discount can bring the total below what the entered line payments sum to.
        // Catch that here instead of silently overcharging.
        if ($this->total_paid > $this->total_after_discount) {
            $this->addError('named_discounts', 'Total payment cannot exceed the amount after discounts. Please adjust the current payment or discount amounts.');
            return;
        }

        // Nothing entered at all -- don't let a click with no payment and no
        // discount still flip the challan's status and consume a receipt number.
        if ($this->total_paid <= 0 && $this->total_discount <= 0) {
            $this->addError('total_paid', 'Enter a payment amount or a discount before submitting.');
            return;
        }

        try {
            app(FeeService::class)->collectChallanPayment($this->challan, [
                'tenant_id'             => session('tenant_id') ?? Auth::user()->tenant_id,
                'receiving_account_id'  => $this->receiving_account_id,
                'discount_account_id'   => $this->discount_account_id,
                'paid_date'             => $this->paid_date,
                'due_date'              => $this->due_date,
                'receipt_no'            => $this->receipt_no,
                'challan_notes'         => $this->challan_notes,
                'paid_by'               => Auth::id(),
                'line_items'            => $this->line_items,
                'named_discounts'       => $this->named_discounts,
                'total_paid'            => $this->total_paid,
                'total_discount'        => $this->total_discount,
                'total_after_discount'  => $this->total_after_discount,
            ]);
        } catch (\RuntimeException $e) {
            $this->addError('total_paid', $e->getMessage());
            return;
        }

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
            'discount_types' => DiscountType::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
