<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class ChallanDiscount extends Model
{
    protected $fillable = [
        'challan_id', 'discount_type_id', 'amount', 'reason'
    ];

    public function challan() { return $this->belongsTo(Challan::class); }
    public function discountType() { return $this->belongsTo(DiscountType::class); }
}
