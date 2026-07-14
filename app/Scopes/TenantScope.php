<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $tenantId = Session::get('tenant_id') ?? config('tenant.current_id');

        if ($tenantId) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        } else {
            // No tenant context resolvable (e.g. a queued job, scheduled command, or
            // `artisan tinker` running outside a request). Fail closed instead of
            // silently returning every tenant's rows: callers that legitimately need
            // to run outside a request must explicitly set config('tenant.current_id')
            // first (see DemoDataSeeder for an example).
            $builder->whereRaw('1 = 0');
        }
    }
}
