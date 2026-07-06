<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartPhrase extends Model
{
    use BelongsToPractice;

    protected $fillable = [
        'practice_id',
        'trigger',
        'expansion',
        'category',
        'owner_id',
        'is_global',
        'is_ai_suggested',
    ];

    protected function casts(): array
    {
        return [
            'is_global' => 'boolean',
            'is_ai_suggested' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
