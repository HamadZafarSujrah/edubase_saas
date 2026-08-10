<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\LeaveRequest;
use Illuminate\Support\Facades\Auth;

class RequestLeave extends Component
{
    public $employee_search = '';
    public $suggested_employees = [];
    public $employee_id = '';
    public $selected_employee = null;

    public $type = 'casual';
    public $from_date;
    public $to_date;
    public $reason = '';

    public function mount()
    {
        $this->from_date = date('Y-m-d');
        $this->to_date = date('Y-m-d');
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
    }

    public function submit()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:sick,casual,annual',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'nullable|string|max:500',
        ]);

        $overlapping = LeaveRequest::where('tenant_id', $tenantId)
            ->where('employee_id', $this->employee_id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('from_date', '<=', $this->to_date)
            ->where('to_date', '>=', $this->from_date)
            ->exists();

        if ($overlapping) {
            $this->addError('from_date', 'This employee already has a pending or approved leave request overlapping these dates.');
            return;
        }

        LeaveRequest::create([
            'employee_id' => $this->employee_id,
            'type' => $this->type,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Leave request submitted for approval.');

        return redirect()->route('hrm.leave-approvals');
    }

    public function render()
    {
        return view('livewire.request-leave')->layout('layouts.app');
    }
}
