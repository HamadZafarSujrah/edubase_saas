<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class EmploymentType extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
