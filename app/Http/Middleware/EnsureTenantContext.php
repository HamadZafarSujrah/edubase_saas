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

        // 1. Logic for Super Admins / Developers
        if ($user->isSuperAdmin()) {
            if (!session()->has('tenant_id')) {
                // If on a dashboard route but no tenant selected, redirect to selection
                if ($request->is('admin/*') || $request->is('dashboard')) {
                    // Unless they are already on the selection page
                    if (!$request->is('select-tenant')) {
                        return redirect()->route('select-tenant');
                    }
                }
            }
        } 
        // 2. Logic for Regular Tenant Users (Teachers, Staff, etc)
        else {
            // For regular users, the tenant_id MUST come from their user record
            if (!session()->has('tenant_id') || session('tenant_id') != $user->tenant_id) {
                session(['tenant_id' => $user->tenant_id]);
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
