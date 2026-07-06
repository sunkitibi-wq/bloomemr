<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $type
 * @property array $fields
 * @property int $version
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class FormTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'fields',
        'version',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<PatientForm, $this> */
    public function patientForms(): HasMany
    {
        return $this->hasMany(PatientForm::class, 'template_id');
    }
}
