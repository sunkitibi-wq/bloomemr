<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PracticeScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // System admins with no practice_id see all practices
        if (auth()->hasUser() && auth()->user()->is_system_admin) {
            return;
        }

        // Resolve from container-bound current_practice (set by IdentifyPractice middleware)
        $practice = app()->bound('current_practice') ? app('current_practice') : null;

        $practiceId = $practice?->id
            ?? (auth()->hasUser() ? auth()->user()->practice_id : null);

        if ($practiceId) {
            $builder->where($model->getTable().'.practice_id', $practiceId);
        }
    }
}
