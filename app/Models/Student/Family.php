<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Family extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'family_no',
        'father_name',
        'father_cnic',
        'mother_name',
        'mother_cnic',
        'guardian_name',
        'guardian_phone',
        'address',
    ];

    /**
     * All students belonging to this family.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
