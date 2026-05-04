<?php

namespace App\Models;

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
        'username',
        'email',
        'password',
        'tenant_id',
        'role',
        'status',
        'contact',
        'power_level',
        'preferences',
        'created_by',
        'updated_by',
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
        'preferences'       => 'array',
    ];

    // ─────────────────────────────────────────────
    // ROLE HELPERS
    // ─────────────────────────────────────────────

    /**
     * Determine if user is a super admin / developer.
     */
    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['Developer', 'Super Admin', 'Supper Admin', 'tenant_super_admin', 'Admin']);
    }

    /**
     * Determine if user is the tenant's own super admin.
     */
    public function isTenantSuperAdmin(): bool
    {
        return $this->role === 'tenant_super_admin';
    }

    // ─────────────────────────────────────────────
    // PERMISSION HELPERS  (merged from Tenant\User)
    // ─────────────────────────────────────────────

    /**
     * Check if the user has a specific granular permission.
     * Tenant super admins inherently have all permissions.
     */
    public function hasPermission(string $permissionStr): bool
    {
        // Tenant super admin or platform super admin — full access
        if ($this->isTenantSuperAdmin() || $this->isSuperAdmin()) {
            return true;
        }

        // Check granular permission row in user_permissions table
        $directPermission = $this->customPermissions()
            ->where('permission', $permissionStr)
            ->first();

        if ($directPermission) {
            return (bool) $directPermission->is_granted;
        }

        return false;
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    /**
     * Granular permissions explicitly assigned to this user.
     */
    public function customPermissions()
    {
        return $this->hasMany(\App\Models\Permission::class, 'user_id');
    }

    /**
     * Dynamic roles relationship (pivot).
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * The tenant this user belongs to.
     * (Inherited via HasTenant trait, but also explicit for clarity)
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
