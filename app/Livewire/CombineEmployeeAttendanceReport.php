<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\Department;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CombineEmployeeAttendanceReport extends Component
{
    public $campus_id = '';
    public $department_id = '';
    public $start_date;
    public $end_date;

    public $rows = [];

    public function mount()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');
        $this->loadReport();
    }

    public function updated($property)
    {
        $this->loadReport();
    }

    public function loadReport()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $employeesQuery = Employee::where('tenant_id', $tenantId)->where('status', 'active');
        if ($this->campus_id) {
            $employeesQuery->where('campus_id', $this->campus_id);
        }
        if ($this->department_id) {
            $employeesQuery->where('department_id', $this->department_id);
        }

        $employees = $employeesQuery->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'emp_no']);
        $employeeIds = $employees->pluck('id')->all();

        if (empty($employeeIds)) {
            $this->rows = [];
            return;
        }

        $counts = EmployeeAttendance::where('tenant_id', $tenantId)
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->select('employee_id', 'status', DB::raw('count(*) as cnt'))
            ->groupBy('employee_id', 'status')
            ->get()
            ->groupBy('employee_id');

        $rows = [];
        foreach ($employees as $employee) {
            $employeeCounts = $counts->get($employee->id, collect());
            $present = (int) ($employeeCounts->firstWhere('status', 'present')->cnt ?? 0);
            $absent  = (int) ($employeeCounts->firstWhere('status', 'absent')->cnt ?? 0);
            $leave   = (int) ($employeeCounts->firstWhere('status', 'leave')->cnt ?? 0);
            $late    = (int) ($employeeCounts->firstWhere('status', 'late')->cnt ?? 0);
            $total   = $present + $absent + $leave + $late;

            $rows[] = [
                'employee'   => $employee,
                'present'    => $present,
                'absent'     => $absent,
                'leave'      => $leave,
                'late'       => $late,
                'total'      => $total,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        }

        $this->rows = $rows;
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.combine-employee-attendance-report', [
            'campuses' => Campus::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'departments' => Department::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
