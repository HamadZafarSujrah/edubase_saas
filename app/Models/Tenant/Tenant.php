<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'domain',
        'subdomain',
        'database',
        'email',
        'address',
        'phone',
        'status', // active, inactive, suspended
        'logo_url',
    ];

    /**
     * Get the users associated with this tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if the tenant is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}
