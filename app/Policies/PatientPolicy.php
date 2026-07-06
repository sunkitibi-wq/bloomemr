<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_patients');
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->hasPermissionTo('view_patients');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_patients');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->hasPermissionTo('update_patients');
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->hasPermissionTo('delete_patients');
    }
}
