<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class StudentVisitor extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'name',
        'relation',
        'phone',
        'address',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
