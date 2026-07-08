<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Database\Factories\EncounterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encounter extends Model
{
    /** @use HasFactory<EncounterFactory> */
    use BelongsToPractice, HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'provider_id',
        'type',
        'status',
        'encounter_date',
        'signed_at',
        'signed_by',
        'supervisor_id',
        'practice_id',
        'chief_complaint',
        'assessment',
        'plan',
        'released_to_portal_at',
    ];

    protected function casts(): array
    {
        return [
            'encounter_date' => 'datetime',
            'signed_at' => 'datetime',
            'released_to_portal_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function signedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function clinicalNotes(): HasMany
    {
        return $this->hasMany(ClinicalNote::class);
    }

    public function radiologyOrders(): HasMany
    {
        return $this->hasMany(RadiologyOrder::class);
    }
}
