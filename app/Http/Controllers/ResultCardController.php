<?php

namespace App\Http\Controllers;

use App\Models\Exam\Exam;
use App\Models\Exam\ExamMark;
use App\Models\Student\Student;
use App\Models\Academic\GradingPolicy;
use App\Models\Tenant;

class ResultCardController extends Controller
{
    public function print($examId, $studentId)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        $exam = Exam::where('tenant_id', $tenantId)->with('schoolClass')->findOrFail($examId);
        $student = Student::where('tenant_id', $tenantId)->findOrFail($studentId);

        $marks = ExamMark::where('tenant_id', $tenantId)
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->with('subject')
            ->get();

        $totalObtained = $marks->sum('marks_obtained');
        $totalMarks = $marks->sum('marks_total');
        $percentage = $totalMarks > 0 ? round(($totalObtained / $totalMarks) * 100, 1) : 0;
        $overallGrade = GradingPolicy::resolveGrade($tenantId, $exam->school_class_id, $percentage);

        $tenant = Tenant::find($tenantId);

        return view('print.result-card', compact('exam', 'student', 'marks', 'totalObtained', 'totalMarks', 'percentage', 'overallGrade', 'tenant'));
    }
}
