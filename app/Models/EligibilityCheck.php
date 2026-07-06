<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EligibilityCheck extends Model
{
    protected $fillable = [
        'practice_id',
        'patient_id',
        'status',
        'copay_amount',
        'deductible_amount',
        'payer_name',
        'checked_at',
    ];

    protected $casts = [
        'copay_amount' => 'decimal:2',
        'deductible_amount' => 'decimal:2',
        'checked_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
