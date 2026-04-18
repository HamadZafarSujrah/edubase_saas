<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant\Tenant;

trait HasTenant
{
    /**
     * Boot the trait and automatically apply the global scope.
     */
    protected static function bootHasTenant()
    {
        // Add global scope to automatically filter by the current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = session('tenant_id') ?? request()->header('X-Tenant-ID') ?? config('tenant.current_id') ?? 1; // Fallback to 1 for dev
            
            if ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            }
        });

        // Automatically assign the current tenant_id when creating new records
        static::creating(function (Model $model) {
            if (!$model->tenant_id) {
                $tenantId = session('tenant_id') ?? request()->header('X-Tenant-ID') ?? config('tenant.current_id') ?? 1; // Fallback to 1 for dev
                if ($tenantId) {
                    $model->tenant_id = $tenantId;
                }
            }
        });
    }

    /**
     * Define the relationship to the Tenant model.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
