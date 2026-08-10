<?php

namespace App\Http\Controllers;

use App\Models\Academic\Section;
use App\Models\Attendance\StudentAttendance;
use App\Models\Student\Student;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceSheetController extends Controller
{
    public function print(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        $section = Section::where('tenant_id', $tenantId)->findOrFail($request->section_id);
        $month = (int) $request->month;
        $year = (int) $request->year;

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $students = Student::where('tenant_id', $tenantId)
            ->where('section_id', $section->id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        $records = StudentAttendance::where('tenant_id', $tenantId)
            ->where('section_id', $section->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy('student_id');

        // grid[student_id][day] = 'P'|'A'|'L'|'T'|''
        $grid = [];
        $statusCode = ['present' => 'P', 'absent' => 'A', 'leave' => 'L', 'late' => 'T'];
        foreach ($students as $s) {
            $row = array_fill(1, $daysInMonth, '');
            foreach ($records->get($s->id, collect()) as $rec) {
                $day = Carbon::parse($rec->date)->day;
                $row[$day] = $statusCode[$rec->status] ?? '';
            }
            $grid[$s->id] = $row;
        }

        $tenant = Tenant::find($tenantId);
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F Y');

        return view('print.attendance-sheet', compact('students', 'grid', 'daysInMonth', 'section', 'monthName', 'tenant'));
    }
}
