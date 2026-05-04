<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class StudentDocument extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'title',
        'file_path',
        'file_type',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
