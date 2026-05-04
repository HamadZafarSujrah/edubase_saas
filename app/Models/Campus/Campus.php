<?php

namespace App\Models\Campus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasTenant;

class Campus extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'contact_email',
        'contact_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    /**
     * All classes assigned to this campus (pivot).
     */
    public function campusClasses()
    {
        return $this->hasMany(\App\Models\Academic\CampusClass::class);
    }

    /**
     * All students enrolled in this campus.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class);
    }

    /**
     * All sections belonging to this campus.
     */
    public function sections()
    {
        return $this->hasMany(\App\Models\Academic\Section::class);
    }

    /**
     * All academic sessions for this campus.
     */
    public function sessions()
    {
        return $this->hasMany(\App\Models\Academic\Session::class);
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    /**
     * Only active campuses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    /**
     * Count of enrolled students.
     */
    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }
}
