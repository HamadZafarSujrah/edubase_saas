<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant\Tenant;

class TenantMiddleware
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
        // 1. Identify tenant from 'X-Tenant-ID' Header (Useful for API/Mobile Apps)
        $tenantId = $request->header('X-Tenant-ID');
        
        // 2. Fallback: Identify tenant from Subdomain (Useful for Web portals)
        if (!$tenantId) {
            $host = $request->getHost(); 
            // e.g. schoolA.edubase.com -> explode returns ['schoolA', 'edubase', 'com']
            $subdomain = explode('.', $host)[0];
            
            // Assume the main application domain is 'edubase' 'localhost' or 'www'
            if (!in_array($subdomain, ['www', 'localhost', 'edubase', 'admin'])) {
                $tenant = Tenant::where('subdomain', $subdomain)->first();
                if ($tenant) {
                    $tenantId = $tenant->id;
                }
            }
        }

        // If no tenant found and this isn't a central domain route
        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant identification failed. Invalid subdomain or missing X-Tenant-ID header.'
            ], 403);
        }

        // Validate the tenant is active
        $tenant = Tenant::find($tenantId);
        if (!$tenant || !$tenant->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant is inactive or suspended. Please contact support.'
            ], 403);
        }

        // Set the active tenant into application config/session so traits can grab it
        config(['tenant.current_id' => $tenant->id]);
        
        return $next($request);
    }
}
