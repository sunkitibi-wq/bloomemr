<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Carbon\Carbon;
use Database\Factories\ClinicalNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $encounter_id
 * @property string $template_type
 * @property array|null $sections
 * @property array|null $draft
 * @property int $version
 * @property string|null $body
 * @property int|null $signed_by
 * @property Carbon|null $signed_at
 * @property int|null $practice_id
 * @property-read Encounter $encounter
 */
class ClinicalNote extends Model
{
    /** @use HasFactory<ClinicalNoteFactory> */
    use BelongsToPractice, HasFactory;

    protected $fillable = [
        'encounter_id',
        'template_type',
        'sections',
        'draft',
        'version',
        'body',
        'signed_by',
        'signed_at',
        'practice_id',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'draft' => 'array',
            'signed_at' => 'datetime',
        ];
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function signedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
