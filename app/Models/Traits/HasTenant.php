<?php

namespace App\Models\Traits;

use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Session;

trait HasTenant
{
    /**
     * The table associated with the tenant.
     * All multi-tenant models must have a tenant_id column.
     */
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            $tenantId = Session::get('tenant_id') ?? config('tenant.current_id');
            if ($tenantId) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    /**
     * Get the tenant that owns this model.
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
