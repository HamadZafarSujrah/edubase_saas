<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class ChallanItem extends Model
{
    protected $fillable = [
        'challan_id', 'fee_particular_id', 'particular_name', 'amount'
    ];

    public function challan() { return $this->belongsTo(Challan::class); }
    public function particular() { return $this->belongsTo(FeeParticular::class); }
}
