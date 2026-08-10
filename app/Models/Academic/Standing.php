<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Standing extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name', 'description'];
}
