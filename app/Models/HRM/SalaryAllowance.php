<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class SalaryAllowance extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'salary_plan_id', 'name', 'amount', 'type'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function salaryPlan()
    {
        return $this->belongsTo(SalaryPlan::class);
    }
}
