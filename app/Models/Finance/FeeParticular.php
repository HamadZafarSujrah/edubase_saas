<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class FeeParticular extends Model
{
    use HasTenant;
    protected $fillable = ['tenant_id', 'name', 'is_active'];
}
