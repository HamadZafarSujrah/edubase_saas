<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Campus\Campus;

class FeePlanParticular extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'campus_id', 'fee_plan_id', 'fee_particular_id',
        'amount', 'min_amount', 'is_first_time',
        'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'
    ];

    protected $casts = [
        'is_first_time' => 'boolean',
        'jan' => 'boolean', 'feb' => 'boolean', 'mar' => 'boolean', 'apr' => 'boolean',
        'may' => 'boolean', 'jun' => 'boolean', 'jul' => 'boolean', 'aug' => 'boolean',
        'sep' => 'boolean', 'oct' => 'boolean', 'nov' => 'boolean', 'dec' => 'boolean',
    ];

    public function campus() { return $this->belongsTo(Campus::class); }
    public function feePlan() { return $this->belongsTo(FeePlan::class); }
    public function particular() { return $this->belongsTo(FeeParticular::class, 'fee_particular_id'); }
}
