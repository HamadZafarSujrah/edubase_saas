<?php

namespace App\Models\Campus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Campus extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'contact_email',
        'contact_phone',
        'is_active',
    ];

    /**
     * Get the classes assigned to this campus.
     */
    // public function classes()
    // {
    //     return $this->hasMany(CampusClass::class);
    // }
}
