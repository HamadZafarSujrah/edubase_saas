<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\SalaryPlan;
use App\Models\HRM\AllowanceType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryPlanManager extends Component
{
    public $employee_search = '';
    public $suggested_employees = [];
    public $employee_id = '';
    public $selected_employee = null;
    public $current_plan = null;

    public $basic_salary;
    public $effective_from;
    // [['name' => ..., 'amount' => ..., 'type' => 'allowance'|'deduction']]
    public $allowance_rows = [];

    public function mount()
    {
        $this->effective_from = date('Y-m-d');
        $this->allowance_rows = [['name' => '', 'amount' => '', 'type' => 'allowance']];
    }

    public function updatedEmployeeSearch()
    {
        if (strlen($this->employee_search) < 2) {
            $this->suggested_employees = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->suggested_employees = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->employee_search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->employee_search . '%')
                  ->orWhere('emp_no', 'like', '%' . $this->employee_search . '%');
            })
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'emp_no'])
            ->toArray();
    }

    public function selectEmployee($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->selected_employee = Employee::where('tenant_id', $tenantId)->find($id);
        $this->employee_id = $id;
        $this->suggested_employees = [];
        $this->employee_search = trim(($this->selected_employee->first_name ?? '') . ' ' . ($this->selected_employee->last_name ?? ''));

        $this->current_plan = SalaryPlan::where('tenant_id', $tenantId)
            ->where('employee_id', $id)
            ->with('allowances')
            ->latest('id')
            ->first();

        $this->basic_salary = $this->current_plan?->basic_salary ?? '';
        $this->allowance_rows = $this->current_plan && $this->current_plan->allowances->count() > 0
            ? $this->current_plan->allowances->map(fn ($a) => ['name' => $a->name, 'amount' => $a->amount, 'type' => $a->type])->toArray()
            : [['name' => '', 'amount' => '', 'type' => 'allowance']];
    }

    public function addAllowanceRow() { $this->allowance_rows[] = ['name' => '', 'amount' => '', 'type' => 'allowance']; }

    public function removeAllowanceRow($index)
    {
        unset($this->allowance_rows[$index]);
        $this->allowance_rows = array_values($this->allowance_rows);
    }

    public function submit()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
        ]);

        DB::transaction(function () use ($tenantId) {
            $plan = SalaryPlan::create([
                'employee_id' => $this->employee_id,
                'basic_salary' => $this->basic_salary,
                'effective_from' => $this->effective_from,
                'status' => 'pending',
                'is_active' => false,
            ]);

            foreach ($this->allowance_rows as $row) {
                if ($row['name'] && $row['amount'] !== '') {
                    $plan->allowances()->create([
                        'name' => $row['name'],
                        'amount' => $row['amount'],
                        'type' => $row['type'],
                    ]);
                }
            }
        });

        session()->flash('message', 'Salary plan submitted for approval.');

        return redirect()->route('hrm.salary-plan-approvals');
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.salary-plan-manager', [
            'allowanceTypes' => AllowanceType::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
