<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectMessage extends Model
{
    protected $fillable = [
        'practice_id',
        'patient_id',
        'patient_form_id',
        'sender_id',
        'sender_address',
        'recipient_address',
        'recipient_name',
        'subject',
        'scope',
        'payload',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function consent(): BelongsTo
    {
        return $this->belongsTo(PatientForm::class, 'patient_form_id');
    }
}
