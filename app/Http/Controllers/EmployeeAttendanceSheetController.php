<?php

namespace App\Http\Controllers;

use App\Models\HRM\Department;
use App\Models\HRM\Employee;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeAttendanceSheetController extends Controller
{
    public function print(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        $department = $request->department_id ? Department::where('tenant_id', $tenantId)->findOrFail($request->department_id) : null;
        $month = (int) $request->month;
        $year = (int) $request->year;

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $employeesQuery = Employee::where('tenant_id', $tenantId)->where('status', 'active');
        if ($department) {
            $employeesQuery->where('department_id', $department->id);
        }
        $employees = $employeesQuery->orderBy('first_name')->get();

        $records = EmployeeAttendance::where('tenant_id', $tenantId)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->whereYear('date', $year)->whereMonth('date', $month)->get()->groupBy('employee_id');

        $grid = [];
        $statusCode = ['present' => 'P', 'absent' => 'A', 'leave' => 'L', 'late' => 'T'];
        foreach ($employees as $e) {
            $row = array_fill(1, $daysInMonth, '');
            foreach ($records->get($e->id, collect()) as $rec) {
                $day = Carbon::parse($rec->date)->day;
                $row[$day] = $statusCode[$rec->status] ?? '';
            }
            $grid[$e->id] = $row;
        }

        $tenant = Tenant::find($tenantId);
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F Y');

        return view('print.employee-attendance-sheet', compact('employees', 'grid', 'daysInMonth', 'department', 'monthName', 'tenant'));
    }
}
