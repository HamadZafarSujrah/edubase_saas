<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class FeePlan extends Model
{
    use HasTenant;
    protected $fillable = ['tenant_id', 'name', 'description', 'is_active'];
}
