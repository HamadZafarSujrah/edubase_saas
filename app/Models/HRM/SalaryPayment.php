<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\User;

class SalaryPayment extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'salary_plan_id', 'month', 'year',
        'basic_salary', 'gross_amount', 'deductions_amount', 'advance_deducted', 'net_amount',
        'status', 'paid_at', 'generated_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'deductions_amount' => 'decimal:2',
        'advance_deducted' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryPlan()
    {
        return $this->belongsTo(SalaryPlan::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
