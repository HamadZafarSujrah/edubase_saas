<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Family;
use App\Models\Finance\Challan;
use App\Models\Finance\GLAccount;
use App\Services\FeeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FamilyWisePayment extends Component
{
    public $family_search = '';
    public $suggested_families = [];
    public $family_id = '';
    public $selected_family = null;

    // [student_id => ['name', 'challan_id', 'challan_no', 'remaining', 'selected']]
    public $rows = [];

    public $receiving_account_id = '';
    public $paid_date;
    public $receipt_no = '';
    public $challan_notes = '';

    public function mount()
    {
        $this->paid_date = date('Y-m-d');
    }

    public function updatedFamilySearch()
    {
        if (strlen($this->family_search) < 2) {
            $this->suggested_families = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->suggested_families = Family::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('father_name', 'like', '%' . $this->family_search . '%')
                  ->orWhere('family_no', 'like', '%' . $this->family_search . '%')
                  ->orWhere('guardian_name', 'like', '%' . $this->family_search . '%');
            })
            ->limit(8)
            ->get(['id', 'family_no', 'father_name', 'guardian_name'])
            ->toArray();
    }

    public function selectFamily($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->selected_family = Family::where('tenant_id', $tenantId)->with('students')->find($id);
        $this->family_id = $id;
        $this->suggested_families = [];
        $this->family_search = $this->selected_family->father_name ?? $this->selected_family->family_no;

        $rows = [];
        foreach ($this->selected_family->students as $student) {
            $challan = Challan::where('tenant_id', $tenantId)
                ->where('student_id', $student->id)
                ->where('status', '!=', 'paid')
                ->orderBy('due_date')
                ->first();

            $remaining = $challan
                ? round((float) $challan->total_amount - (float) $challan->paid_amount - (float) $challan->discount_amount, 2)
                : 0;

            $rows[$student->id] = [
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'challan_id' => $challan?->id,
                'challan_no' => $challan?->challan_no,
                'remaining' => $remaining,
                'selected' => $challan && $remaining > 0,
            ];
        }

        $this->rows = $rows;
    }

    public function getTotalSelectedProperty()
    {
        return array_sum(array_map(
            fn ($r) => !empty($r['selected']) ? (float) $r['remaining'] : 0,
            $this->rows
        ));
    }

    public function submit()
    {
        $this->validate([
            'receiving_account_id' => 'required|exists:gl_accounts,id',
            'paid_date' => 'required|date',
        ]);

        $selectedRows = array_filter($this->rows, fn ($r) => !empty($r['selected']) && $r['challan_id'] && $r['remaining'] > 0);

        if (empty($selectedRows)) {
            $this->addError('rows', 'Select at least one student with an outstanding balance.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $feeService = app(FeeService::class);

        try {
            DB::transaction(function () use ($selectedRows, $tenantId, $feeService) {
                foreach ($selectedRows as $row) {
                    $challan = Challan::with('items')->findOrFail($row['challan_id']);
                    $remaining = (float) $row['remaining'];

                    $weights = $challan->items->pluck('amount')->map(fn ($a) => (float) $a)->toArray();
                    $names = $challan->items->pluck('particular_name')->toArray();

                    if (empty($weights)) {
                        continue;
                    }

                    $shares = $feeService->splitProportional($remaining, $weights);

                    $lineItems = [];
                    foreach ($names as $i => $name) {
                        if ($shares[$i] <= 0) continue;
                        $lineItems[] = ['name' => $name, 'current_payment' => $shares[$i], 'discount' => 0];
                    }

                    $feeService->collectChallanPayment($challan, [
                        'tenant_id' => $tenantId,
                        'receiving_account_id' => $this->receiving_account_id,
                        'discount_account_id' => null,
                        'paid_date' => $this->paid_date,
                        'due_date' => $challan->due_date,
                        'receipt_no' => $this->receipt_no,
                        'challan_notes' => $this->challan_notes,
                        'paid_by' => Auth::id(),
                        'line_items' => $lineItems,
                        'named_discounts' => [],
                        'total_paid' => $remaining,
                        'total_discount' => 0,
                        'total_after_discount' => $remaining,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            $this->addError('rows', $e->getMessage());
            return;
        }

        session()->flash('message', 'Family-wise payment recorded for ' . count($selectedRows) . ' student(s).');
        $this->reset(['family_search', 'suggested_families', 'family_id', 'selected_family', 'rows', 'receipt_no', 'challan_notes']);
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.family-wise-payment', [
            'receiving_accounts' => GLAccount::where('tenant_id', $tenantId)
                ->where('is_inactive', 0)
                ->whereIn('type', ['income', 'asset'])
                ->get(),
        ])->layout('layouts.app');
    }
}
