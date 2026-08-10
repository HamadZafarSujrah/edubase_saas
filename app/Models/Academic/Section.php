<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Section extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'school_class_id',
        'campus_id',
        'teacher_id',    // employee assigned as class teacher for this section
        'name',          // e.g. "A", "B", "Rose", "Tulip"
        'room_number',   // optional physical room
        'capacity',      // Maximum students allowed
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity'  => 'integer',
    ];

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    /**
     * The class this section belongs to.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    /**
     * The campus this section belongs to.
     */
    public function campus()
    {
        return $this->belongsTo(\App\Models\Campus\Campus::class);
    }

    /**
     * The employee (teacher) assigned to this section.
     */
    public function teacher()
    {
        return $this->belongsTo(\App\Models\HRM\Employee::class, 'teacher_id');
    }

    /**
     * All students in this section.
     */
    public function students()
    {
        return $this->hasMany(\App\Models\Student\Student::class);
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    /**
     * Only active sections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    /**
     * Check if the section has reached its maximum capacity.
     */
    public function isAtCapacity(): bool
    {
        if (!$this->capacity) return false;
        return $this->students()->count() >= $this->capacity;
    }

    /**
     * Number of available seats remaining.
     */
    public function getAvailableSeatsAttribute(): int
    {
        if (!$this->capacity) return 999;
        return max(0, $this->capacity - $this->students()->count());
    }
}
