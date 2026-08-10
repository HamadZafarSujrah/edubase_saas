<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Academic\Session;
use App\Models\Academic\SchoolClass;

class Exam extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'session_id', 'school_class_id', 'name', 'type', 'exam_date'];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }

    public function subjectSchedules()
    {
        return $this->hasMany(ExamSubjectSchedule::class);
    }
}
