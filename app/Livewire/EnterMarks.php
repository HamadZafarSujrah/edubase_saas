<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamMark;
use App\Models\Academic\ClassSubject;
use App\Models\Academic\GradingPolicy;
use App\Models\Academic\Section;
use App\Models\Student\Student;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnterMarks extends Component
{
    public $exam_id = '';
    public $section_id = '';

    public $exam = null;
    public $subjects = [];
    public $sections = [];

    // [student_id => ['name' => .., 'admission_no' => .., 'marks' => [subject_id => ['obtained' => .., 'total' => ..]]]]
    public $roster = [];
    public $loaded = false;

    public $sms_result_message = '';

    public function updatedExamId()
    {
        $this->section_id = '';
        $this->loaded = false;
        $this->loadClassContext();
    }

    private function loadClassContext()
    {
        if (!$this->exam_id) {
            $this->exam = null;
            $this->subjects = [];
            $this->sections = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->exam = Exam::where('tenant_id', $tenantId)->with('schoolClass')->findOrFail($this->exam_id);
        $this->subjects = ClassSubject::where('tenant_id', $tenantId)
            ->where('school_class_id', $this->exam->school_class_id)
            ->with('subject')
            ->get();
        $this->sections = Section::where('tenant_id', $tenantId)->where('school_class_id', $this->exam->school_class_id)->get();
    }

    public function loadRoster()
    {
        if (!$this->exam_id) {
            session()->flash('error', 'Select an exam first.');
            return;
        }

        if (empty($this->subjects)) {
            session()->flash('error', 'This class has no subjects assigned yet. Assign subjects to the class first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $studentsQuery = Student::where('tenant_id', $tenantId)
            ->where('school_class_id', $this->exam->school_class_id)
            ->where('is_active', true);
        if ($this->section_id) {
            $studentsQuery->where('section_id', $this->section_id);
        }
        $students = $studentsQuery->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'admission_no']);

        $existingMarks = ExamMark::where('tenant_id', $tenantId)
            ->where('exam_id', $this->exam_id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $roster = [];
        foreach ($students as $student) {
            $marks = [];
            $studentMarks = $existingMarks->get($student->id, collect())->keyBy('subject_id');
            foreach ($this->subjects as $cs) {
                $existing = $studentMarks->get($cs->subject_id);
                $marks[$cs->subject_id] = [
                    'obtained' => $existing?->marks_obtained ?? '',
                    'total' => $cs->marks_total,
                ];
            }
            $roster[$student->id] = [
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'admission_no' => $student->admission_no,
                'marks' => $marks,
            ];
        }

        $this->roster = $roster;
        $this->loaded = true;
        $this->sms_result_message = '';
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
                foreach ($entry['marks'] as $subjectId => $mark) {
                    if ($mark['obtained'] === '' || $mark['obtained'] === null) {
                        continue;
                    }

                    $percentage = $mark['total'] > 0 ? ($mark['obtained'] / $mark['total']) * 100 : 0;
                    $grade = GradingPolicy::resolveGrade($tenantId, $this->exam->school_class_id, $percentage);

                    ExamMark::updateOrCreate(
                        ['tenant_id' => $tenantId, 'exam_id' => $this->exam_id, 'student_id' => $studentId, 'subject_id' => $subjectId],
                        ['marks_total' => $mark['total'], 'marks_obtained' => $mark['obtained'], 'grade' => $grade]
                    );
                }
            }
        });

        session()->flash('message', 'Marks saved for ' . count($this->roster) . ' student(s).');
        $this->loadRoster();
    }

    public function sendResultSms()
    {
        if (empty($this->roster)) {
            session()->flash('error', 'Load a roster first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $sms = new SmsService();
        $sent = 0;
        $skipped = 0;

        $students = Student::where('tenant_id', $tenantId)->whereIn('id', array_keys($this->roster))->get()->keyBy('id');

        foreach ($this->roster as $studentId => $entry) {
            $student = $students->get($studentId);
            if (!$student || empty($student->primary_phone)) {
                $skipped++;
                continue;
            }

            $marks = ExamMark::where('tenant_id', $tenantId)
                ->where('exam_id', $this->exam_id)
                ->where('student_id', $studentId)
                ->whereNotNull('marks_obtained')
                ->get();

            // No marks entered for this student yet -- nothing to report, so
            // don't send a nonsensical "0/0 (0%)" result to their parent.
            if ($marks->isEmpty()) {
                $skipped++;
                continue;
            }

            $obtained = $marks->sum('marks_obtained');
            $total = $marks->sum('marks_total');
            $percentage = $total > 0 ? round(($obtained / $total) * 100, 1) : 0;

            $message = "Result for {$entry['name']} in {$this->exam->name}: {$obtained}/{$total} ({$percentage}%).";

            $sms->sendAndLog([
                'tenant_id' => $tenantId,
                'student_id' => $studentId,
                'phone' => $student->primary_phone,
                'recipient_name' => $student->father_name ?: $entry['name'],
                'message' => $message,
                'type' => 'result',
                'sent_by' => Auth::id(),
            ]);
            $sent++;
        }

        $this->sms_result_message = "Result SMS sent to {$sent} parent(s). {$skipped} skipped (no phone on file).";
    }

    public function render()
    {
        return view('livewire.enter-marks', [
            'exams' => Exam::where('tenant_id', session('tenant_id') ?? Auth::user()->tenant_id)->with('schoolClass')->orderByDesc('exam_date')->get(),
        ])->layout('layouts.app');
    }
}
