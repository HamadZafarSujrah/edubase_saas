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

class TakeAttendance extends Component
{
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
    public $date;

    // [student_id => ['status' => ..., 'remarks' => ...]]
    public $roster = [];
    public $loaded = false;
    public $result_message = '';

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updatedCampusId() { $this->school_class_id = ''; $this->section_id = ''; $this->loaded = false; }
    public function updatedSchoolClassId() { $this->section_id = ''; $this->loaded = false; }
    public function updatedSectionId() { $this->loaded = false; }
    public function updatedDate() { $this->loaded = false; }

    public function loadRoster()
    {
        if (!$this->section_id || !$this->date) {
            session()->flash('error', 'Select a campus, class, section, and date first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $students = Student::where('tenant_id', $tenantId)
            ->where('section_id', $this->section_id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'admission_no']);

        $existing = StudentAttendance::where('tenant_id', $tenantId)
            ->where('section_id', $this->section_id)
            ->where('date', $this->date)
            ->get()
            ->keyBy('student_id');

        $roster = [];
        foreach ($students as $s) {
            $mark = $existing->get($s->id);
            $roster[$s->id] = [
                'name'         => trim($s->first_name . ' ' . $s->last_name),
                'admission_no' => $s->admission_no,
                // Default new/unmarked students to present -- mark-all-present
                // then flip exceptions is the fast path for a full class.
                'status'       => $mark->status ?? 'present',
                'remarks'      => $mark->remarks ?? '',
            ];
        }

        $this->roster = $roster;
        $this->loaded = true;
        $this->result_message = $existing->count() > 0
            ? 'Attendance already recorded for this date -- editing existing records.'
            : '';
    }

    public function markAll($status)
    {
        foreach ($this->roster as $id => $entry) {
            $this->roster[$id]['status'] = $status;
        }
    }

    public function save()
    {
        if (empty($this->roster)) {
            session()->flash('error', 'Load a roster first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        DB::transaction(function () use ($tenantId) {
            foreach ($this->roster as $studentId => $entry) {
                StudentAttendance::updateOrCreate(
                    ['tenant_id' => $tenantId, 'student_id' => $studentId, 'date' => $this->date],
                    [
                        'section_id' => $this->section_id,
                        'status'     => $entry['status'],
                        'remarks'    => $entry['remarks'] ?: null,
                        'marked_by'  => Auth::id(),
                    ]
                );
            }
        });

        session()->flash('message', 'Attendance saved for ' . count($this->roster) . ' student(s) on ' . \Carbon\Carbon::parse($this->date)->format('d-M-Y') . '.');
        $this->loadRoster();
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.take-attendance', [
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
            'classes'  => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections' => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
        ])->layout('layouts.app');
    }
}
