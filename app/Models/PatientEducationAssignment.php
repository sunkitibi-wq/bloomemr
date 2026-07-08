<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientEducationAssignment extends Model
{
    use BelongsToPractice;

    protected $fillable = [
        'practice_id',
        'patient_id',
        'education_article_id',
        'assigned_by',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(EducationArticle::class, 'education_article_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
