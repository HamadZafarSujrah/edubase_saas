<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Student\Student;
use App\Models\Finance\FeePlan;
use App\Models\Finance\FeePlanParticular;
use App\Models\Finance\StudentFeePlanItem;
use Carbon\Carbon;

class StudentFeePlanEditor extends Component
{
    // Student & context
    public $student_id;
    public $current_tenant_id;

    // Mode: create | edit | view
    public $mode    = 'create';
    public $viewOnly = false;

    // Determine whether the current user can edit fee plans
    protected function userCanEdit(): bool
    {
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }


    public $fee_plan_year      = '';
    public $with_effect_from   = '';
    public $percentage_increment = '';
    public $discount_type      = 'NO DISCOUNT';
    public $notes              = '';

    // Fee Plan switching
    public $selected_fee_plan_id = '';

    // Line items: array of [ particular_id, name, is_first_time, first_time_arrears, actual_fee, discount, fee_after_discount ]
    public $line_items = [];

    // Discount types for dropdown
    public $discount_types = [
        'NO DISCOUNT',
        'SIBLING DISCOUNT',
        'STAFF DISCOUNT',
        'SCHOLARSHIP',
        'SPECIAL DISCOUNT',
        'EARLY PAYMENT',
        'OTHER',
    ];

    public function mount()
    {
        $this->current_tenant_id = session('tenant_id') ?? auth()->user()->tenant_id;
        $this->student_id = request()->query('sid');
        $this->viewOnly = request()->has('view');

        if (!$this->student_id) {
            abort(400, 'Student ID is required.');
        }

        // Retrieve student, handling possible deletion
        try {
            $student = Student::findOrFail($this->student_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Student was deleted – send user back to the list with a message
            session()->flash('message', 'Student not found – it may have been removed.');
            $this->redirect(route('create.fee-plans'));
            return;
        }

        $this->selected_fee_plan_id = $student->fee_plan_id;
        $this->fee_plan_year = $student->fee_plan_year ?? '';
        $this->with_effect_from = $student->fee_plan_effect_from ?? '';
        $this->percentage_increment = $student->fee_plan_increment ?? '';
        $this->discount_type = $student->fee_plan_discount_type ?? 'NO DISCOUNT';
        $this->notes = $student->fee_plan_notes ?? '';

        // Determine mode based on permission and existing data
        if ($this->viewOnly) {
            $this->mode = 'view';
        } elseif ($this->userCanEdit()) {
            // Edit mode only if there are existing items
            $existingItems = StudentFeePlanItem::with('particular')
                ->where('student_id', $this->student_id)
                ->get();

            if ($existingItems->isNotEmpty()) {
                $this->mode = 'edit';
                $this->loadFromExistingItems($existingItems);
            } elseif ($student->fee_plan_id) {
                $this->loadFromMasterPlan($student->fee_plan_id, $student);
            }
        } else {
            // User cannot edit – force view mode
            $this->mode = 'view';
            $existingItems = StudentFeePlanItem::with('particular')
                ->where('student_id', $this->student_id)
                ->get();
            if ($existingItems->isNotEmpty()) {
                $this->loadFromExistingItems($existingItems);
            } elseif ($student->fee_plan_id) {
                $this->loadFromMasterPlan($student->fee_plan_id, $student);
            }
        }
    }

    protected function loadFromExistingItems($items)
    {
        $this->line_items = [];
        foreach ($items as $item) {
            $this->line_items[] = [
                'item_id'           => $item->id,
                'particular_id'     => $item->fee_particular_id,
                'name'              => $item->particular->name ?? 'Unknown',
                'is_first_time'     => (bool)($item->particular->is_first_time ?? false),
                'first_time_arrears'=> 0,
                'actual_fee'        => $item->actual_amount,
                'discount'          => $item->discount_amount ?? 0,
                'fee_after_discount'=> ($item->actual_amount - ($item->discount_amount ?? 0)),
            ];
        }

        // Hydrate the global discount inputs from the first item since they are saved homogeneously
        if ($items->first()->discount_reason) {
            $reasonRaw = $items->first()->discount_reason;
            if (strpos($reasonRaw, ':') !== false) {
                list($type, $note) = explode(':', $reasonRaw, 2);
                $this->discount_type = trim($type);
                $this->notes = trim($note);
            } else {
                $this->discount_type = trim($reasonRaw);
            }
        }
    }

    protected function loadFromMasterPlan($feePlanId, $student)
    {
        $particulars = FeePlanParticular::with('particular')
            ->where('fee_plan_id', $feePlanId)
            ->get();

        $this->line_items = [];
        foreach ($particulars as $mp) {
            $actualFee = $mp->amount;
            $discount  = 0;

            $this->line_items[] = [
                'item_id'           => null, // Not saved yet
                'particular_id'     => $mp->fee_particular_id,
                'name'              => $mp->particular->name ?? 'Unknown',
                'is_first_time'     => (bool)$mp->is_first_time,
                'first_time_arrears'=> 0,
                'actual_fee'        => round($actualFee, 0),
                'discount'          => $discount,
                'fee_after_discount'=> round($actualFee - $discount, 0),
            ];
        }
    }

    public function updatedLineItems($value, $key)
    {
        // Auto-recalculate fee_after_discount when actual_fee or discount changes
        [$index, $field] = explode('.', $key);

        if (in_array($field, ['actual_fee', 'discount'])) {
            $actualFee = (float)($this->line_items[$index]['actual_fee'] ?? 0);
            $discount  = (float)($this->line_items[$index]['discount'] ?? 0);
            $this->line_items[$index]['fee_after_discount'] = max(0, $actualFee - $discount);
        }
    }

    public function updatedSelectedFeePlanId($value)
    {
        if (!$value) return;

        $student = Student::findOrFail($this->student_id);
        $student->update(['fee_plan_id' => $value]);
        $this->selected_fee_plan_id = $value;

        // Reload line items from new master plan
        $this->loadFromMasterPlan($value, $student);
        session()->flash('message', 'Fee plan template switched. Review and save the amounts below.');
    }

    public function save()
    {
        $this->validate([
            'student_id'       => 'required|exists:students,id',
            'line_items'       => 'required|array|min:1',
        ]);

        if ($this->mode === 'view') {
            // View‑only – do not persist any changes
            session()->flash('message', 'You are viewing the fee plan – no changes were saved.');
            return redirect()->route('finance.view-edit-fee-plans');
        }

        DB::transaction(function () {
            $tenantId = $this->current_tenant_id;
            $student  = Student::findOrFail($this->student_id);

            // Save fee plan metadata to student
            $student->update([
                'fee_plan_effect_from'   => $this->with_effect_from,
                'fee_plan_increment'     => $this->percentage_increment ?: null,
                'fee_plan_year'          => $this->fee_plan_year,
                'fee_plan_discount_type' => $this->discount_type,
                'fee_plan_notes'         => $this->notes,
            ]);

            // Build a list of IDs that should stay (those present in the UI)
            $keptItemIds = collect($this->line_items)->pluck('item_id')->filter()->toArray();
            // Delete any orphaned items belonging to this student
            StudentFeePlanItem::where('tenant_id', $tenantId)
                ->where('student_id', $this->student_id)
                ->whereNotIn('id', $keptItemIds)
                ->delete();

            foreach ($this->line_items as $item) {
                $data = [
                    'tenant_id'        => $tenantId,
                    'student_id'       => $this->student_id,
                    'fee_particular_id'=> $item['particular_id'],
                    'actual_amount'    => $item['actual_fee'],
                    'discount_amount'  => $item['discount'],
                    'discount_reason'  => $this->discount_type !== 'NO DISCOUNT' ? $this->discount_type . ($this->notes ? ': ' . $this->notes : '') : null,
                ];

                if (!empty($item['item_id'])) {
                    // Update existing
                    StudentFeePlanItem::where('id', $item['item_id'])->update($data);
                } else {
                    // Create new
                    StudentFeePlanItem::create($data);
                }
            }
        });

        // Determine where to redirect based on what we just did
        if ($this->mode === 'edit') {
            session()->flash('message', 'Student fee plan updated successfully!');
            return redirect()->route('finance.view-edit-fee-plans');
        } else {
            session()->flash('message', 'Fee plan created successfully for student!');
            return redirect()->route('create.fee-plans');
        }
    }

    // Computed totals for the summary row
    public function getTotalsProperty()
    {
        return [
            'first_time_arrears' => collect($this->line_items)->sum('first_time_arrears'),
            'actual_fee'         => collect($this->line_items)->sum('actual_fee'),
            'discount'           => collect($this->line_items)->sum('discount'),
            'fee_after_discount' => collect($this->line_items)->sum('fee_after_discount'),
        ];
    }

    public function render()
    {
        return view('livewire.student-fee-plan-editor', [
            // Pass mode information to the view so Blade can disable fields
            'mode' => $this->mode,
            'viewOnly' => $this->viewOnly,
            'student'   => Student::with(['campus', 'schoolClass', 'section', 'feePlan', 'academicSession'])->findOrFail($this->student_id),
            'fee_plans' => FeePlan::where('tenant_id', $this->current_tenant_id)->where('is_active', true)->get(),
            'totals'    => $this->totals,
        ])->layout('layouts.app');
    }
}
