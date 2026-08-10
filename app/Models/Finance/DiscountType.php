<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class DiscountType extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name', 'type', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
