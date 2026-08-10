<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\Attendance\EmployeeAttendance;
use Illuminate\Support\Facades\Auth;

class IndividualEmployeeAttendanceReport extends Component
{
    public $employee_search = '';
    public $suggested_employees = [];
    public $employee_id = '';
    public $selected_employee = null;

    public $start_date;
    public $end_date;

    public $records = [];
    public $summary = ['present' => 0, 'absent' => 0, 'leave' => 0, 'late' => 0, 'total' => 0, 'percentage' => 0];

    public function mount()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');
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
        $this->selected_employee = Employee::where('tenant_id', $tenantId)
            ->with(['department', 'designation', 'campus'])
            ->find($id);
        $this->employee_id = $id;
        $this->suggested_employees = [];
        $this->employee_search = trim(($this->selected_employee->first_name ?? '') . ' ' . ($this->selected_employee->last_name ?? ''));
        $this->loadReport();
    }

    public function updated($property)
    {
        if (in_array($property, ['start_date', 'end_date']) && $this->employee_id) {
            $this->loadReport();
        }
    }

    public function loadReport()
    {
        if (!$this->employee_id) {
            $this->records = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->records = EmployeeAttendance::where('tenant_id', $tenantId)
            ->where('employee_id', $this->employee_id)
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->orderBy('date', 'desc')
            ->get();

        $present = $this->records->where('status', 'present')->count();
        $absent  = $this->records->where('status', 'absent')->count();
        $leave   = $this->records->where('status', 'leave')->count();
        $late    = $this->records->where('status', 'late')->count();
        $total   = $this->records->count();

        $this->summary = [
            'present'    => $present,
            'absent'     => $absent,
            'leave'      => $leave,
            'late'       => $late,
            'total'      => $total,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
        ];
    }

    public function render()
    {
        return view('livewire.individual-employee-attendance-report')->layout('layouts.app');
    }
}
