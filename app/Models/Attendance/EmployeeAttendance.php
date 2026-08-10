<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\HRM\Employee;
use App\Models\User;

class EmployeeAttendance extends Model
{
    use HasTenant;

    protected $table = 'employee_attendance';

    protected $fillable = [
        'tenant_id', 'employee_id', 'date', 'time_in', 'time_out', 'status', 'method', 'remarks', 'marked_by',
        'is_locked', 'locked_by', 'locked_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
