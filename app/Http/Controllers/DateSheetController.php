<?php

namespace App\Http\Controllers;

use App\Models\Exam\Exam;
use App\Models\Exam\ExamSubjectSchedule;
use App\Models\Tenant;

class DateSheetController extends Controller
{
    public function print($examId)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        $exam = Exam::where('tenant_id', $tenantId)->with('schoolClass')->findOrFail($examId);

        $schedules = ExamSubjectSchedule::where('tenant_id', $tenantId)
            ->where('exam_id', $examId)
            ->with('subject')
            ->orderBy('date')
            ->get();

        $tenant = Tenant::find($tenantId);

        return view('print.date-sheet', compact('exam', 'schedules', 'tenant'));
    }
}
