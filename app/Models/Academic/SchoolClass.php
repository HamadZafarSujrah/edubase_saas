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

    protected $casts = [
        'is_active'     => 'boolean',
        'numeric_value' => 'integer',
    ];

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    /**
     * All sections for this class.
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'school_class_id');
    }

    /**
     * Campus assignments for this class (pivot).
     */
    public function campusClasses()
    {
        return $this->hasMany(CampusClass::class, 'school_class_id');
    }

    /**
     * All students enrolled in this class.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class, 'school_class_id');
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    /**
     * Only active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    /**
     * Number of students in this class.
     */
    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }
}
