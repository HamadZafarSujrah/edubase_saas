<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\Department;
use App\Models\HRM\SalaryPayment;
use App\Models\HRM\SalaryAdvance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GenerateSalary extends Component
{
    public $department_id = '';
    public $month;
    public $year;
    public $showOnlyPending = true;

    public function mount()
    {
        $this->month = date('m');
        $this->year = date('Y');
    }

    public function getEligibleEmployeesProperty()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereHas('activeSalaryPlan')
            ->with(['activeSalaryPlan.allowances']);

        if ($this->department_id) {
            $query->where('department_id', $this->department_id);
        }

        if ($this->showOnlyPending) {
            $query->whereDoesntHave('salaryPayments', function ($q) {
                $q->where('month', $this->month)->where('year', $this->year);
            });
        }

        return $query->orderBy('first_name')->get();
    }

    public function generate()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereHas('activeSalaryPlan')
            ->with(['activeSalaryPlan.allowances'])
            ->when($this->department_id, fn ($q) => $q->where('department_id', $this->department_id))
            ->get();

        $generated = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            $plan = $employee->activeSalaryPlan;
            if (!$plan) {
                continue;
            }

            $exists = SalaryPayment::where('tenant_id', $tenantId)
                ->where('employee_id', $employee->id)
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($tenantId, $employee, $plan) {
                    $outstandingAdvances = SalaryAdvance::where('tenant_id', $tenantId)
                        ->where('employee_id', $employee->id)
                        ->where('status', 'approved')
                        ->where('deducted', false)
                        ->get();
                    $advanceTotal = $outstandingAdvances->sum('amount');

                    SalaryPayment::create([
                        'tenant_id' => $tenantId,
                        'employee_id' => $employee->id,
                        'salary_plan_id' => $plan->id,
                        'month' => $this->month,
                        'year' => $this->year,
                        'basic_salary' => $plan->basic_salary,
                        'gross_amount' => $plan->gross,
                        'deductions_amount' => $plan->deductions,
                        'advance_deducted' => $advanceTotal,
                        'net_amount' => $plan->net - $advanceTotal,
                        'status' => 'pending',
                        'generated_by' => Auth::id(),
                    ]);

                    if ($outstandingAdvances->isNotEmpty()) {
                        SalaryAdvance::whereIn('id', $outstandingAdvances->pluck('id'))->update(['deducted' => true]);
                    }
                });
                $generated++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Unique (tenant_id, employee_id, month, year) collision -- someone
                // else generated this employee's salary in the meantime. Skip it.
                $skipped++;
            }
        }

        session()->flash('message', "Generated {$generated} salary payment(s). Skipped {$skipped} (already generated).");
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.generate-salary', [
            'departments' => Department::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
