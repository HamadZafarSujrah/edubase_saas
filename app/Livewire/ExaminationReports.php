<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamMark;
use App\Models\Student\Student;
use Illuminate\Support\Facades\Auth;

class ExaminationReports extends Component
{
    public $exam_id = '';

    public $student_search = '';
    public $suggested_students = [];
    public $result_card_student_id = '';

    public function updatedExamId()
    {
        $this->student_search = '';
        $this->suggested_students = [];
        $this->result_card_student_id = '';
    }

    public function updatedStudentSearch()
    {
        if (strlen($this->student_search) < 2 || !$this->exam_id) {
            $this->suggested_students = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $exam = Exam::where('tenant_id', $tenantId)->find($this->exam_id);
        if (!$exam) {
            return;
        }

        $this->suggested_students = Student::where('tenant_id', $tenantId)
            ->where('school_class_id', $exam->school_class_id)
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->student_search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->student_search . '%');
            })
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'admission_no'])
            ->toArray();
    }

    public function selectResultCardStudent($id)
    {
        $selected = collect($this->suggested_students)->firstWhere('id', $id);
        $this->result_card_student_id = $id;
        $this->student_search = $selected ? trim($selected['first_name'] . ' ' . $selected['last_name']) : '';
        $this->suggested_students = [];
    }

    public function getClassPerformanceProperty()
    {
        if (!$this->exam_id) {
            return collect();
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $marks = ExamMark::where('tenant_id', $tenantId)
            ->where('exam_id', $this->exam_id)
            ->with('student')
            ->get()
            ->groupBy('student_id');

        $rows = [];
        foreach ($marks as $studentId => $studentMarks) {
            $student = $studentMarks->first()->student;
            if (!$student) continue;
            $obtained = $studentMarks->sum('marks_obtained');
            $total = $studentMarks->sum('marks_total');
            $rows[] = [
                'student' => $student,
                'obtained' => $obtained,
                'total' => $total,
                'percentage' => $total > 0 ? round(($obtained / $total) * 100, 1) : 0,
            ];
        }

        usort($rows, fn ($a, $b) => $b['percentage'] <=> $a['percentage']);
        foreach ($rows as $i => &$row) {
            $row['rank'] = $i + 1;
        }

        return collect($rows);
    }

    public function getSubjectAnalysisProperty()
    {
        if (!$this->exam_id) {
            return collect();
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $marks = ExamMark::where('tenant_id', $tenantId)
            ->where('exam_id', $this->exam_id)
            ->whereNotNull('marks_obtained')
            ->with('subject')
            ->get()
            ->groupBy('subject_id');

        $rows = [];
        foreach ($marks as $subjectId => $subjectMarks) {
            $subject = $subjectMarks->first()->subject;
            if (!$subject) continue;
            $percentages = $subjectMarks->map(fn ($m) => $m->marks_total > 0 ? ($m->marks_obtained / $m->marks_total) * 100 : 0);
            $rows[] = [
                'subject' => $subject,
                'average' => round($percentages->avg(), 1),
                'highest' => round($percentages->max(), 1),
                'lowest' => round($percentages->min(), 1),
                'pass_rate' => round($percentages->filter(fn ($p) => $p >= 40)->count() / max(1, $percentages->count()) * 100, 1),
                'count' => $subjectMarks->count(),
            ];
        }

        return collect($rows);
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.examination-reports', [
            'exams' => Exam::where('tenant_id', $tenantId)->with('schoolClass')->orderByDesc('exam_date')->get(),
            'classPerformance' => $this->classPerformance,
            'subjectAnalysis' => $this->subjectAnalysis,
        ])->layout('layouts.app');
    }
}
