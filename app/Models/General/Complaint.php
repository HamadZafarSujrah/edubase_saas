<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Student\Student;
use App\Models\User;

class Complaint extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'student_id', 'subject', 'description', 'complainant_name', 'complainant_phone',
        'status', 'assigned_to', 'resolution_notes', 'resolved_at', 'created_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
