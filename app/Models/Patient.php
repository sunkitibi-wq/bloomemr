<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use BelongsToPractice, HasFactory, SoftDeletes;

    protected $fillable = [
        'mrn',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender_identity',
        'pronouns',
        'race_ethnicity',
        'preferred_language',
        'address',
        'phone',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'primary_insurance',
        'secondary_insurance',
        'allergies',
        'problem_list',
        'photo_path',
        'primary_provider_id',
        'practice_id',
        'portal_user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function primaryProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_provider_id');
    }

    /** @return BelongsTo<User, $this> */
    public function portalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'portal_user_id');
    }

    /** @return HasMany<Encounter, $this> */
    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }

    /** @return HasMany<Document, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /** @return HasMany<AuditLog, $this> */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /** @return HasMany<Assessment, $this> */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /** @return HasMany<Medication, $this> */
    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }

    /** @return HasMany<Prescription, $this> */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /** @return HasMany<LabOrder, $this> */
    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class);
    }

    /** @return HasMany<LabResult, $this> */
    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class);
    }

    /** @return HasMany<MedicationTeachingLog, $this> */
    public function medicationTeachingLogs(): HasMany
    {
        return $this->hasMany(MedicationTeachingLog::class);
    }

    /** @return HasMany<PatientEngagement, $this> */
    public function engagements(): HasMany
    {
        return $this->hasMany(PatientEngagement::class);
    }

    /** @return HasMany<Appointment, $this> */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /** @return HasMany<Invoice, $this> */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** @return HasMany<SecureMessage, $this> */
    public function secureMessages(): HasMany
    {
        return $this->hasMany(SecureMessage::class);
    }

    /** @return HasMany<RefillRequest, $this> */
    public function refillRequests(): HasMany
    {
        return $this->hasMany(RefillRequest::class);
    }

    /** @return HasMany<PatientForm, $this> */
    public function patientForms(): HasMany
    {
        return $this->hasMany(PatientForm::class);
    }

    /** @return HasMany<DirectMessage, $this> */
    public function directMessages(): HasMany
    {
        return $this->hasMany(DirectMessage::class);
    }

    /** @return HasMany<RadiologyOrder, $this> */
    public function radiologyOrders(): HasMany
    {
        return $this->hasMany(RadiologyOrder::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }
}
