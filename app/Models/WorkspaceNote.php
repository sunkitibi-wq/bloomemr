<?php

namespace App\Models;

use Database\Factories\WorkspaceNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceNote extends Model
{
    /** @use HasFactory<WorkspaceNoteFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'target_date',
    ];

    protected $casts = [
        'target_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
