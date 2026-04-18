<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class SchoolClass extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'school_classes';

    protected $fillable = [
        'tenant_id',
        'name',
        'numeric_value',
        'is_active',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class, 'school_class_id');
    }
}
