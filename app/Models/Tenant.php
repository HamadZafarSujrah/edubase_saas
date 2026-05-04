<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'domain',
        'subdomain',
        'database',
        'email',
        'address',
        'phone',
        'status',
        'logo_url',
        'student_prefix',
        'next_student_number'
    ];

    /**
     * Check if the tenant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    /**
     * Get all users belonging to this tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all campuses belonging to this tenant.
     */
    public function campuses()
    {
        return $this->hasMany(\App\Models\Campus\Campus::class);
    }

    /**
     * Get all students belonging to this tenant.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class);
    }

    /**
     * Get all accounting groups for this tenant.
     */
    public function glGroups()
    {
        return $this->hasMany(\App\Models\Accounting\GLGroup::class);
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    /**
     * Scope to get only active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
