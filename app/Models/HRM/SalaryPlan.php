<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\User;

class SalaryPlan extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'basic_salary', 'frequency', 'effective_from',
        'status', 'is_active', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'effective_from' => 'date',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function allowances()
    {
        return $this->hasMany(SalaryAllowance::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getGrossAttribute()
    {
        return $this->basic_salary + $this->allowances->where('type', 'allowance')->sum('amount');
    }

    public function getDeductionsAttribute()
    {
        return $this->allowances->where('type', 'deduction')->sum('amount');
    }

    public function getNetAttribute()
    {
        return $this->gross - $this->deductions;
    }
}
