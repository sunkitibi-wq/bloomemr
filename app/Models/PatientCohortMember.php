<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientCohortMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id',
        'patient_id',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(PatientCohort::class, 'cohort_id');
    }
}
