<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationArticle extends Model
{
    use BelongsToPractice;

    protected $fillable = [
        'practice_id',
        'title',
        'category',
        'content',
        'video_url',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(PatientEducationAssignment::class);
    }

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'patient_education_assignments')
            ->withPivot('acknowledged_at', 'assigned_by')
            ->withTimestamps();
    }
}
