<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Attendance\StudentAttendance;
use Illuminate\Support\Facades\Auth;

class IndividualAttendanceReport extends Component
{
    public $student_search = '';
    public $suggested_students = [];
    public $student_id = '';
    public $selected_student = null;

    public $start_date;
    public $end_date;

    public $records = [];
    public $summary = ['present' => 0, 'absent' => 0, 'leave' => 0, 'late' => 0, 'total' => 0, 'percentage' => 0];

    public function mount()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');
    }

    public function updatedStudentSearch()
    {
        if (strlen($this->student_search) < 2) {
            $this->suggested_students = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->suggested_students = Student::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->student_search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->student_search . '%')
                  ->orWhere('father_name', 'like', '%' . $this->student_search . '%');
            })
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'admission_no', 'father_name'])
            ->toArray();
    }

    public function selectStudent($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->selected_student = Student::where('tenant_id', $tenantId)
            ->with(['schoolClass', 'section', 'campus'])
            ->find($id);
        $this->student_id = $id;
        $this->suggested_students = [];
        $this->student_search = trim(($this->selected_student->first_name ?? '') . ' ' . ($this->selected_student->last_name ?? ''));
        $this->loadReport();
    }

    public function updated($property)
    {
        if (in_array($property, ['start_date', 'end_date']) && $this->student_id) {
            $this->loadReport();
        }
    }

    public function loadReport()
    {
        if (!$this->student_id) {
            $this->records = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->records = StudentAttendance::where('tenant_id', $tenantId)
            ->where('student_id', $this->student_id)
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
        return view('livewire.individual-attendance-report')->layout('layouts.app');
    }
}
