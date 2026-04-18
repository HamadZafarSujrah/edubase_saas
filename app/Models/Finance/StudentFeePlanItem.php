<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class StudentFeePlanItem extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'student_id', 'fee_particular_id',
        'actual_amount', 'discount_amount', 'discount_reason',
        'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'
    ];

    public function particular() { return $this->belongsTo(FeeParticular::class, 'fee_particular_id'); }
}
