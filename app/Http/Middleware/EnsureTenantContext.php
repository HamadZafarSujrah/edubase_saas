<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class EnsureTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Ignore for unauthenticated users (login, etc)
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // 1. Any user with a tenant_id on their own record belongs to exactly
        // that tenant -- this includes "super admin" roles that are scoped to
        // one institution (tenant_super_admin, Admin), not just regular
        // staff. Always bind session tenant_id from the user record rather
        // than gating this on a route-pattern allowlist: the previous check
        // only ran this sync when visiting admin/* or /dashboard, so landing
        // on any OTHER tenant-scoped page first (a bookmark, a direct nav
        // click) left session('tenant_id') unset for the rest of the
        // session -- every tenant-scoped query then failed closed (returned
        // zero rows) with no error and no indication why.
        if ($user->tenant_id) {
            if (!session()->has('tenant_id') || session('tenant_id') != $user->tenant_id) {
                session(['tenant_id' => $user->tenant_id]);
            }
        }
        // 2. True platform-level admins (Developer / Super Admin) have no
        // tenant_id of their own and must actively choose which institution
        // to operate as.
        elseif ($user->isSuperAdmin()) {
            if (!session()->has('tenant_id')) {
                if (!$request->is('select-tenant')) {
                    return redirect()->route('select-tenant');
                }
            }
        }

        // 3. Validation and Global Config Binding
        if (session()->has('tenant_id')) {
            $tenantId = session('tenant_id');
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                session()->forget('tenant_id');
                return redirect()->route('login')->with('error', 'Invalid tenant session.');
            }

            if (!$tenant->isActive()) {
                session()->forget('tenant_id');
                return redirect()->route('login')->with('error', 'Your institution is currently inactive.');
            }

            // Sync with central tenant config for convenience across the app
            config(['tenant.current_id' => $tenant->id]);
            config(['tenant.current' => $tenant]);
            
            // Share with all views automatically
            view()->share('currentTenant', $tenant);
        }

        return $next($request);
    }
}
