<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Student\Student;

class Challan extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'student_id', 'challan_no', 'month', 'year', 
        'issue_date', 'due_date', 'total_amount', 'payable_after_due', 'status'
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function items() { return $this->hasMany(ChallanItem::class); }
}
