<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use BelongsToPractice, HasFactory;

    protected $fillable = [
        'patient_id',
        'uploaded_by',
        'practice_id',
        'category',
        'original_name',
        'file_path',
        'mime_type',
        'size',
        'ocr_text',
        'version',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
