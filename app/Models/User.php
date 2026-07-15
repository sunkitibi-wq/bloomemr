<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string|null $npi_number
 * @property string|null $dea_number
 * @property string|null $phone
 * @property string $timezone
 * @property bool $is_active
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $practice_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'npi_number', 'dea_number', 'phone', 'timezone', 'is_system_admin', 'kyc_status', 'kyc_data', 'kyc_rejection_reason', 'subscribed_until'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use BelongsToPractice, HasApiTokens, HasFactory, HasRoles, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected static function booted(): void
    {
        static::created(function (User $user) {
            if ($user->role) {
                Role::findOrCreate($user->role);
                $user->assignRole($user->role);
            }
        });

        static::updated(function (User $user) {
            if ($user->wasChanged('role') && $user->role) {
                Role::findOrCreate($user->role);
                $user->syncRoles($user->role);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_system_admin' => 'boolean',
            'subscribed_until' => 'datetime',
            'kyc_data' => 'array',
        ];
    }

    public function isSubscribed(): bool
    {
        if ($this->subscribed_until && $this->subscribed_until->isFuture()) {
            return true;
        }

        if ($this->practice && $this->practice->is_enterprise && $this->practice->enterprise_expires_at && $this->practice->enterprise_expires_at->isFuture()) {
            return true;
        }

        return false;
    }

    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /** @return HasMany<Patient, $this> */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'primary_provider_id');
    }

    /** @return HasMany<Patient, $this> */
    public function portalPatients(): HasMany
    {
        return $this->hasMany(Patient::class, 'portal_user_id');
    }

    /** @return HasMany<Encounter, $this> */
    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class, 'provider_id');
    }

    /** @return HasMany<Appointment, $this> */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'provider_id');
    }

    /** @return HasMany<Document, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /** @return HasMany<SmartPhrase, $this> */
    public function smartPhrases(): HasMany
    {
        return $this->hasMany(SmartPhrase::class, 'owner_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isSystemAdmin(): bool
    {
        return (bool) $this->is_system_admin;
    }

    public function isPharmacist(): bool
    {
        return $this->role === 'pharmacist';
    }

    public function pharmacyTeamMembers(): HasMany
    {
        return $this->hasMany(User::class, 'practice_id', 'practice_id')
            ->where('id', '!=', $this->id);
    }

    /** @param Builder<User> $query */
    public function scopeByPractice(Builder $query, int $practiceId): void
    {
        $query->where('practice_id', $practiceId);
    }

    /** @return HasMany<SecureMessage, $this> */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(SecureMessage::class, 'sender_id');
    }

    /** @return HasMany<SecureMessage, $this> */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(SecureMessage::class, 'recipient_id');
    }

    /** @return HasMany<RefillRequest, $this> */
    public function refillRequests(): HasMany
    {
        return $this->hasMany(RefillRequest::class, 'requested_by');
    }
}
