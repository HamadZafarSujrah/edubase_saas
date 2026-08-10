<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Student\Student;
use App\Models\Academic\Subject;

class ExamMark extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'exam_id', 'student_id', 'subject_id', 'marks_obtained', 'marks_total', 'grade', 'remarks'];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'marks_total' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getPercentageAttribute()
    {
        return $this->marks_total > 0 ? round(($this->marks_obtained / $this->marks_total) * 100, 1) : 0;
    }
}
