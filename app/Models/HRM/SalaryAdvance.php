<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\User;

class SalaryAdvance extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'amount', 'reason', 'status', 'deducted', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deducted' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
