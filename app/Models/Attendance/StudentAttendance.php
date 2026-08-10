<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Student\Student;
use App\Models\Academic\Section;
use App\Models\User;

class StudentAttendance extends Model
{
    use HasTenant;

    protected $table = 'student_attendance';

    protected $fillable = [
        'tenant_id', 'student_id', 'section_id', 'date', 'status', 'remarks', 'marked_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
