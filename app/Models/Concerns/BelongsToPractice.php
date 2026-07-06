<?php

namespace App\Models\Concerns;

use App\Models\Practice;
use App\Models\Scopes\PracticeScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToPractice
{
    public static function bootBelongsToPractice(): void
    {
        static::addGlobalScope(new PracticeScope);

        static::creating(function ($model) {
            if ($model->practice_id) {
                return;
            }

            // Resolve from the container-bound current_practice first
            $practice = app()->bound('current_practice') ? app('current_practice') : null;

            if ($practice) {
                $model->practice_id = $practice->id;
            } elseif (auth()->check() && auth()->user()->practice_id) {
                $model->practice_id = auth()->user()->practice_id;
            }
        });
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }
}
