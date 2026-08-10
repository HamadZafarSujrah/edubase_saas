<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class AllowanceType extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name', 'type', 'description'];
}
