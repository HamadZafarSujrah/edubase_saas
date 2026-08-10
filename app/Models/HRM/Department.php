<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Department extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'department_group_id', 'name'];

    public function group()
    {
        return $this->belongsTo(DepartmentGroup::class, 'department_group_id');
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
