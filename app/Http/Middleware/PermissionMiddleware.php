<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     * Ensure the user has the required permission for the current action.
     * Also enforce that only the tenant's super admin can modify permissions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // The "tenant_super_admin" inherently bypasses all permission checks 
        // because they own the school account.
        if ($user->role === 'tenant_super_admin') {
            return $next($request);
        }

        // If the route relates to altering permissions, explicitly block anyone 
        // who is NOT the tenant_super_admin. This adheres to the rule that ONLY 
        // the super admin of the tenant can assign these module checkboxes natively.
        if ($permission === 'assign_permissions') {
            return response()->json([
                'success' => false, 
                'message' => 'Access Denied: Only the Super Admin of this school can alter user permissions.'
            ], 403);
        }

        // For all other actions (e.g. 'student.add', 'fee.edit'), check if the 
        // user was natively granted that specific granular right.
        if ($permission && !$user->hasPermission($permission)) {
            return response()->json([
                'success' => false, 
                'message' => "Access Denied: You lack the '{$permission}' permission."
            ], 403);
        }

        return $next($request);
    }
}
