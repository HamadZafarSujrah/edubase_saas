<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Attendance\StudentAttendance;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CombineAttendanceReport extends Component
{
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
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
        if ($property === 'campus_id') {
            $this->school_class_id = '';
            $this->section_id = '';
        }
        if ($property === 'school_class_id') {
            $this->section_id = '';
        }
        $this->loadReport();
    }

    public function loadReport()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $studentsQuery = Student::where('tenant_id', $tenantId)->where('is_active', true);
        if ($this->campus_id) {
            $studentsQuery->where('campus_id', $this->campus_id);
        }
        if ($this->school_class_id) {
            $studentsQuery->where('school_class_id', $this->school_class_id);
        }
        if ($this->section_id) {
            $studentsQuery->where('section_id', $this->section_id);
        }

        $students = $studentsQuery->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'admission_no']);
        $studentIds = $students->pluck('id')->all();

        if (empty($studentIds)) {
            $this->rows = [];
            return;
        }

        $counts = StudentAttendance::where('tenant_id', $tenantId)
            ->whereIn('student_id', $studentIds)
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->select('student_id', 'status', DB::raw('count(*) as cnt'))
            ->groupBy('student_id', 'status')
            ->get()
            ->groupBy('student_id');

        $rows = [];
        foreach ($students as $student) {
            $studentCounts = $counts->get($student->id, collect());
            $present = (int) ($studentCounts->firstWhere('status', 'present')->cnt ?? 0);
            $absent  = (int) ($studentCounts->firstWhere('status', 'absent')->cnt ?? 0);
            $leave   = (int) ($studentCounts->firstWhere('status', 'leave')->cnt ?? 0);
            $late    = (int) ($studentCounts->firstWhere('status', 'late')->cnt ?? 0);
            $total   = $present + $absent + $leave + $late;

            $rows[] = [
                'student'    => $student,
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

        return view('livewire.combine-attendance-report', [
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
            'classes'  => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections' => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
        ])->layout('layouts.app');
    }
}
