<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class EmployeeCustomFieldDef extends Model
{
    use HasTenant;

    protected $table = 'employee_custom_field_defs';

    protected $fillable = ['tenant_id', 'label'];
}
