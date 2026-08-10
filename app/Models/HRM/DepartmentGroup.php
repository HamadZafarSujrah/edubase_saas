<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class DepartmentGroup extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
