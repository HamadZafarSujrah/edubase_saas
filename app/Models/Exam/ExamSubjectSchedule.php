<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Academic\Subject;

class ExamSubjectSchedule extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'exam_id', 'subject_id', 'date', 'start_time', 'end_time', 'room'];

    protected $casts = [
        'date' => 'date',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
