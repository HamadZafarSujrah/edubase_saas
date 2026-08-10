<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamSubjectSchedule;
use App\Models\Academic\ClassSubject;
use Illuminate\Support\Facades\Auth;

class DateSheetManager extends Component
{
    public $exam_id = '';
    public $exam = null;

    // [subject_id => ['name' => .., 'date' => .., 'start_time' => .., 'end_time' => .., 'room' => ..]]
    public $schedule = [];
    public $loaded = false;

    public function updatedExamId()
    {
        $this->loaded = false;
        $this->loadSchedule();
    }

    public function loadSchedule()
    {
        if (!$this->exam_id) {
            $this->exam = null;
            $this->schedule = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->exam = Exam::where('tenant_id', $tenantId)->with('schoolClass')->findOrFail($this->exam_id);

        $classSubjects = ClassSubject::where('tenant_id', $tenantId)
            ->where('school_class_id', $this->exam->school_class_id)
            ->with('subject')
            ->get();

        if ($classSubjects->isEmpty()) {
            session()->flash('error', 'This class has no subjects assigned yet. Assign subjects to the class first.');
            $this->loaded = false;
            return;
        }

        $existing = ExamSubjectSchedule::where('tenant_id', $tenantId)
            ->where('exam_id', $this->exam_id)
            ->get()
            ->keyBy('subject_id');

        $schedule = [];
        foreach ($classSubjects as $cs) {
            $row = $existing->get($cs->subject_id);
            $schedule[$cs->subject_id] = [
                'name' => $cs->subject->name ?? '—',
                'date' => $row?->date?->format('Y-m-d') ?? '',
                'start_time' => $row && $row->start_time ? substr($row->start_time, 0, 5) : '',
                'end_time' => $row && $row->end_time ? substr($row->end_time, 0, 5) : '',
                'room' => $row->room ?? '',
            ];
        }

        $this->schedule = $schedule;
        $this->loaded = true;
    }

    public function save()
    {
        if (empty($this->schedule)) {
            session()->flash('error', 'Load a date sheet first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        foreach ($this->schedule as $subjectId => $row) {
            if (!$row['date']) {
                continue;
            }

            ExamSubjectSchedule::updateOrCreate(
                ['tenant_id' => $tenantId, 'exam_id' => $this->exam_id, 'subject_id' => $subjectId],
                [
                    'date' => $row['date'],
                    'start_time' => $row['start_time'] ?: null,
                    'end_time' => $row['end_time'] ?: null,
                    'room' => $row['room'] ?: null,
                ]
            );
        }

        session()->flash('message', 'Date sheet saved successfully.');
        $this->loadSchedule();
    }

    public function render()
    {
        return view('livewire.date-sheet-manager', [
            'exams' => Exam::where('tenant_id', session('tenant_id') ?? Auth::user()->tenant_id)->with('schoolClass')->orderByDesc('exam_date')->get(),
        ])->layout('layouts.app');
    }
}
