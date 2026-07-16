<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use BelongsToPractice, HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'encounter_id',
        'practice_id',
        'cpt_codes',
        'total_amount',
        'status',
        'insurance_claim_status',
        'claim_reference',
        'claim_note',
        'submitted_at',
        'accepted_at',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'cpt_codes' => 'array',
            'due_date' => 'date',
            'submitted_at' => 'datetime',
            'accepted_at' => 'datetime',
            'total_amount' => 'decimal:2',
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

    public function claimSubmissions(): HasMany
    {
        return $this->hasMany(ClaimSubmission::class);
    }
}
