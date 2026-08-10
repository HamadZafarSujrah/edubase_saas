<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class ClassShift extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name', 'start_time', 'end_time'];

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class, 'class_shift_id');
    }
}
