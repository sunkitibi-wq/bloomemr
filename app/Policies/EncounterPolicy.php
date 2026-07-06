<?php

namespace App\Policies;

use App\Models\Encounter;
use App\Models\User;

class EncounterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_encounters');
    }

    public function view(User $user, Encounter $encounter): bool
    {
        if ($user->hasPermissionTo('view_encounters')) {
            if ($user->hasRole(['attending', 'resident'])) {
                return true;
            }

            return $encounter->provider_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_encounters');
    }

    public function update(User $user, Encounter $encounter): bool
    {
        if ($encounter->status === 'signed') {
            return $user->hasPermissionTo('sign_encounters');
        }

        return $encounter->provider_id === $user->id;
    }

    public function delete(User $user, Encounter $encounter): bool
    {
        return $user->hasPermissionTo('delete_encounters');
    }

    public function sign(User $user, Encounter $encounter): bool
    {
        return $user->hasPermissionTo('sign_encounters');
    }
}
