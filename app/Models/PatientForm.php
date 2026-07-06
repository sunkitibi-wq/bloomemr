<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $patient_id
 * @property int $practice_id
 * @property int $template_id
 * @property string $status
 * @property array|null $form_data
 * @property string|null $signature_name
 * @property string|null $signature_ip
 * @property \Illuminate\Support\Carbon|null $signed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PatientForm extends Model
{
    use BelongsToPractice, HasFactory;

    protected $fillable = [
        'patient_id',
        'practice_id',
        'template_id',
        'status',
        'form_data',
        'signature_name',
        'signature_ip',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'signed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Patient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /** @return BelongsTo<FormTemplate, $this> */
    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'template_id');
    }
}
