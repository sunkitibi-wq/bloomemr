<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabResult extends Model
{
    use BelongsToPractice, HasFactory;

    protected $fillable = [
        'practice_id',
        'patient_id',
        'lab_order_id',
        'result_data',
        'is_critical',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'result_data' => 'array',
            'is_critical' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function labOrder(): BelongsTo
    {
        return $this->belongsTo(LabOrder::class);
    }
}
