<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_documents');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('view_documents');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_documents');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('delete_documents');
    }
}
