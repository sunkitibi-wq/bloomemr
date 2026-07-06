<?php

namespace App\Policies;

use App\Models\ClinicalNote;
use App\Models\User;

class ClinicalNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_clinical_notes');
    }

    public function view(User $user, ClinicalNote $clinicalNote): bool
    {
        return $user->hasPermissionTo('view_clinical_notes');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_clinical_notes');
    }

    public function update(User $user, ClinicalNote $clinicalNote): bool
    {
        return $clinicalNote->encounter->provider_id === $user->id;
    }

    public function delete(User $user, ClinicalNote $clinicalNote): bool
    {
        return $user->hasPermissionTo('delete_clinical_notes');
    }

    public function sign(User $user, ClinicalNote $clinicalNote): bool
    {
        return $user->hasPermissionTo('sign_clinical_notes');
    }
}
