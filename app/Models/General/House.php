<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Student\Student;

class House extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name', 'color', 'motto'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
