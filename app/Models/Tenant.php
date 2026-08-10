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
        'primary_color',
        'plan_id',
        'trial_ends_at',
        'subscription_status',
        'student_prefix',
        'next_student_number'
    ];

    protected $casts = [
        'trial_ends_at' => 'date',
    ];

    /**
     * Check if the tenant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Whether a module is enabled for this tenant. Reads the `enabled_modules`
     * key from tenant_settings (an array of module slugs); if the tenant has
     * never had this configured, every module is enabled -- new/existing
     * tenants aren't broken by a gate they never opted into.
     */
    public function hasModule(string $slug): bool
    {
        $setting = \App\Models\Finance\TenantSetting::where('tenant_id', $this->id)
            ->where('key', 'enabled_modules')
            ->first();

        if (!$setting || !is_array($setting->value)) {
            return true;
        }

        return in_array($slug, $setting->value, true);
    }

    /**
     * Whether creating one more of $resource ('students' or 'campuses') would
     * exceed this tenant's plan limit. A null limit (or no plan at all) means
     * unlimited -- always returns false in that case.
     */
    public function wouldExceedPlanLimit(string $resource, int $currentCount): bool
    {
        $plan = $this->plan;
        if (!$plan) {
            return false;
        }

        $limitColumn = $resource === 'campuses' ? 'max_campuses' : 'max_students';
        $limit = $plan->{$limitColumn};

        if ($limit === null) {
            return false;
        }

        return ($currentCount + 1) > $limit;
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
