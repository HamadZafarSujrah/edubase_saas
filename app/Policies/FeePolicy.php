<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Finance\Challan;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermission('fee.challan.payments');
    }

    public function view(User $user, Challan $challan): bool
    {
        return $user->isSuperAdmin() || 
               ($user->tenant_id === $challan->tenant_id && $user->hasPermission('fee.challan.payments'));
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermission('fee.challan.generate');
    }

    public function update(User $user, Challan $challan): bool
    {
        return $user->isSuperAdmin() || 
               ($user->tenant_id === $challan->tenant_id && $user->hasPermission('fee.challan.payments'));
    }

    public function delete(User $user, Challan $challan): bool
    {
        return $user->isSuperAdmin() || 
               ($user->tenant_id === $challan->tenant_id && $user->hasPermission('fee.challan.payments'));
    }
}
