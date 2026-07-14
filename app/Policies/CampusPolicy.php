<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campus\Campus;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampusPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('campuses.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('campuses.create');
    }

    public function update(User $user, Campus $campus): bool
    {
        return $user->tenant_id === $campus->tenant_id && $user->hasPermission('campuses.edit');
    }

    public function delete(User $user, Campus $campus): bool
    {
        return $user->tenant_id === $campus->tenant_id && $user->hasPermission('campuses.delete');
    }
}
