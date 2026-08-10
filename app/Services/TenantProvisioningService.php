<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantProvisioningService
{
    /**
     * Creates a new tenant plus its first tenant_super_admin user, used by both
     * the Super Admin Portal's "create tenant" flow and public self-registration.
     *
     * HasTenant::creating() force-assigns tenant_id from session('tenant_id')
     * (session wins over config('tenant.current_id')) on every create -- so if
     * the caller (typically a platform super admin) currently has some OTHER
     * tenant active in their session, creating the admin User naively here
     * would silently attach it to that wrong tenant instead of the brand-new
     * one. Swap the session tenant_id for the duration of this call and always
     * restore it afterward, success or failure.
     *
     * Expected $data: name, code, subdomain (all tenant-unique, pre-validated
     * by the caller), email, admin_name, admin_username, admin_email,
     * admin_password (plain, hashed here).
     */
    public function provision(array $data): array
    {
        $tenant = Tenant::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'subdomain' => $data['subdomain'],
            'email' => $data['email'] ?? null,
            'status' => 'active',
            'plan_id' => Plan::where('slug', 'basic')->value('id'),
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $originalSessionTenantId = session('tenant_id');
        session(['tenant_id' => $tenant->id]);

        try {
            $admin = User::create([
                'tenant_id' => $tenant->id,
                'name' => $data['admin_name'],
                'username' => $data['admin_username'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role' => 'tenant_super_admin',
                'status' => 'active',
            ]);
        } finally {
            if ($originalSessionTenantId !== null) {
                session(['tenant_id' => $originalSessionTenantId]);
            } else {
                session()->forget('tenant_id');
            }
        }

        return ['tenant' => $tenant, 'admin' => $admin];
    }
}
