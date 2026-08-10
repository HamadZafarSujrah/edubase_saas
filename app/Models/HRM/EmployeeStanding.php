<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class EmployeeStanding extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name', 'description'];
}
