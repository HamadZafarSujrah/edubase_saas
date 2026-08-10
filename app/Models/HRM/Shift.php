<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Shift extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name', 'start_time', 'end_time'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
