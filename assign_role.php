<?php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

$role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
$permission = Permission::firstOrCreate(['name' => 'finance.create', 'guard_name' => 'web']);
$role->givePermissionTo($permission);

$user = User::first();
if ($user) {
    if (!$user->hasRole('super-admin')) {
        $user->assignRole($role);
    }
}
echo "Super-admin role created and assigned successfully.\n";
