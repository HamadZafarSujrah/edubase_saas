<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class GradingPolicy extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'school_class_id', 'grade_label', 'min_percent', 'max_percent'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Resolve the grade label for a percentage, preferring a class-specific
     * policy over the tenant-wide default (school_class_id null) when both exist.
     */
    public static function resolveGrade($tenantId, $schoolClassId, $percentage)
    {
        $policy = static::where('tenant_id', $tenantId)
            ->where('school_class_id', $schoolClassId)
            ->where('min_percent', '<=', $percentage)
            ->where('max_percent', '>=', $percentage)
            ->first();

        if (!$policy) {
            $policy = static::where('tenant_id', $tenantId)
                ->whereNull('school_class_id')
                ->where('min_percent', '<=', $percentage)
                ->where('max_percent', '>=', $percentage)
                ->first();
        }

        return $policy?->grade_label;
    }
}
