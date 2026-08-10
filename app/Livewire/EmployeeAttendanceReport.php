<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\Department;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeAttendanceReport extends Component
{
    public $campus_id = '';
    public $department_id = '';
    public $start_date;
    public $end_date;

    public $rows = [];
    public $grand_totals = ['present' => 0, 'absent' => 0, 'leave' => 0, 'late' => 0, 'total' => 0];

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

        $employeeIdsQuery = Employee::where('tenant_id', $tenantId)->where('status', 'active');
        if ($this->campus_id) {
            $employeeIdsQuery->where('campus_id', $this->campus_id);
        }
        if ($this->department_id) {
            $employeeIdsQuery->where('department_id', $this->department_id);
        }
        $employeeIds = $employeeIdsQuery->pluck('id')->all();

        if (empty($employeeIds)) {
            $this->rows = [];
            $this->grand_totals = ['present' => 0, 'absent' => 0, 'leave' => 0, 'late' => 0, 'total' => 0];
            return;
        }

        // Alias to "day" (not "date") so the collection groupBy() below keys on the
        // raw string -- "date" is Carbon-cast on the model, and grouping by a cast
        // attribute stringifies each key via Carbon's __toString() (adds a time part).
        $counts = EmployeeAttendance::where('tenant_id', $tenantId)
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->select(DB::raw('date as day'), 'status', DB::raw('count(*) as cnt'))
            ->groupBy('date', 'status')
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('day');

        $rows = [];
        $grand = ['present' => 0, 'absent' => 0, 'leave' => 0, 'late' => 0, 'total' => 0];

        foreach ($counts as $date => $dayCounts) {
            $present = (int) ($dayCounts->firstWhere('status', 'present')->cnt ?? 0);
            $absent  = (int) ($dayCounts->firstWhere('status', 'absent')->cnt ?? 0);
            $leave   = (int) ($dayCounts->firstWhere('status', 'leave')->cnt ?? 0);
            $late    = (int) ($dayCounts->firstWhere('status', 'late')->cnt ?? 0);
            $total   = $present + $absent + $leave + $late;

            $rows[] = [
                'date'       => $date,
                'present'    => $present,
                'absent'     => $absent,
                'leave'      => $leave,
                'late'       => $late,
                'total'      => $total,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];

            $grand['present'] += $present;
            $grand['absent']  += $absent;
            $grand['leave']   += $leave;
            $grand['late']    += $late;
            $grand['total']   += $total;
        }

        $this->rows = $rows;
        $this->grand_totals = $grand;
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.employee-attendance-report', [
            'campuses' => Campus::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'departments' => Department::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
