<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\User;

class LeaveRequest extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'type', 'from_date', 'to_date', 'reason', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
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
