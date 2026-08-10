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

class AttendanceSummary extends Component
{
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
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

        $studentIdsQuery = Student::where('tenant_id', $tenantId)->where('is_active', true);
        if ($this->campus_id) {
            $studentIdsQuery->where('campus_id', $this->campus_id);
        }
        if ($this->school_class_id) {
            $studentIdsQuery->where('school_class_id', $this->school_class_id);
        }
        if ($this->section_id) {
            $studentIdsQuery->where('section_id', $this->section_id);
        }
        $studentIds = $studentIdsQuery->pluck('id')->all();

        if (empty($studentIds)) {
            $this->rows = [];
            $this->grand_totals = ['present' => 0, 'absent' => 0, 'leave' => 0, 'late' => 0, 'total' => 0];
            return;
        }

        // Alias to "day" (not "date") so the collection groupBy() below keys on the
        // raw string -- "date" is Carbon-cast on the model, and grouping by a cast
        // attribute stringifies each key via Carbon's __toString() (adds a time part).
        $counts = StudentAttendance::where('tenant_id', $tenantId)
            ->whereIn('student_id', $studentIds)
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

        return view('livewire.attendance-summary', [
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
            'classes'  => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections' => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
        ])->layout('layouts.app');
    }
}
