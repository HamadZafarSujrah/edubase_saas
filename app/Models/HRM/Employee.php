<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Campus\Campus;
use App\Models\Academic\Section;

class Employee extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'campus_id', 'department_id', 'designation_id', 'shift_id', 'employment_type_id',
        'emp_no', 'first_name', 'last_name', 'cnic', 'gender', 'phone', 'email', 'photo_path',
        'status', 'join_date', 'address', 'custom_fields',
    ];

    protected $casts = [
        'join_date' => 'date',
        'custom_fields' => 'array',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function sectionsTaught()
    {
        return $this->hasMany(Section::class, 'teacher_id');
    }

    public function attendance()
    {
        return $this->hasMany(\App\Models\Attendance\EmployeeAttendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function salaryPlans()
    {
        return $this->hasMany(SalaryPlan::class);
    }

    public function activeSalaryPlan()
    {
        return $this->hasOne(SalaryPlan::class)->where('is_active', true)->where('status', 'approved');
    }

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function salaryAdvances()
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
