<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Student\Student;
use App\Models\User;

class ChallanVoidLog extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'challan_id', 'challan_no', 'student_id', 'student_name',
        'voided_amount', 'void_reason', 'voided_by',
    ];

    protected $casts = [
        'voided_amount' => 'decimal:2',
    ];

    public function challan() { return $this->belongsTo(Challan::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function voidedBy() { return $this->belongsTo(User::class, 'voided_by'); }
}
