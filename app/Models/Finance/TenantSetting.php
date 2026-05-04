<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    use HasFactory;

    protected $table = 'tenant_settings';
    
    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'array',
    ];
}
