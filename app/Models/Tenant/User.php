<?php

namespace App\Models\Tenant;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HasTenant;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role', // e.g. 'tenant_super_admin', 'teacher', 'accountant', etc.
        'status', // 'active', 'inactive'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the explicitly assigned custom user permissions.
     */
    public function customPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Check if the user has a specific permission.
     * The Tenant Super Admin inherently has all permissions.
     */
    public function hasPermission($permissionStr)
    {
        if ($this->role === 'tenant_super_admin') {
            return true;
        }

        // Check if the user has a direct permission override in the user_permissions table
        $directPermission = $this->customPermissions()->where('permission', $permissionStr)->first();

        if ($directPermission) {
            return (bool) $directPermission->is_granted;
        }

        // Feature enhancement: If no direct permission exists, you may fallback to checking default 
        // permissions associated with the `$this->role` defined in constants.
        
        return false;
    }
}
