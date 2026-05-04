<?php

use App\Models\User;
use App\Models\Tenant;

$user = User::withoutGlobalScopes()
    ->where('role', 'Supper Admin')
    ->orWhere('role', 'Developer')
    ->first();

if ($user && $user->tenant_id) {
    $tenant = Tenant::find($user->tenant_id);
    echo "INSTITUTION_CODE=" . ($tenant ? $tenant->code : 'NOT_FOUND') . "\n";
} else {
    echo "INSTITUTION_CODE=NO_USER_FOUND\n";
}
