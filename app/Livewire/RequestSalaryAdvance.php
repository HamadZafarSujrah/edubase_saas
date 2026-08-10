<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\SalaryAdvance;
use Illuminate\Support\Facades\Auth;

class RequestSalaryAdvance extends Component
{
    public $employee_search = '';
    public $suggested_employees = [];
    public $employee_id = '';
    public $selected_employee = null;

    public $amount;
    public $reason = '';

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
    }

    public function submit()
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:500',
        ]);

        SalaryAdvance::create([
            'employee_id' => $this->employee_id,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Salary advance request submitted for approval.');

        return redirect()->route('hrm.salary-advance-approvals');
    }

    public function render()
    {
        return view('livewire.request-salary-advance')->layout('layouts.app');
    }
}
