<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Database\Factories\AssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    /** @use HasFactory<AssessmentFactory> */
    use BelongsToPractice, HasFactory;

    protected $fillable = [
        'patient_id',
        'encounter_id',
        'practice_id',
        'instrument',
        'rater_type',
        'responses',
        'score',
        'severity_band',
    ];

    protected function casts(): array
    {
        return [
            'responses' => 'array',
            'score' => 'integer',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }
}
