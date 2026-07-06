<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    use BelongsToPractice, HasFactory;

    protected $table = 'asset_maintenances';

    protected $fillable = [
        'practice_id',
        'asset_name',
        'serial_number',
        'status',
        'last_calibrated_at',
        'next_calibration_due',
    ];

    protected $casts = [
        'last_calibrated_at' => 'datetime',
        'next_calibration_due' => 'datetime',
    ];
}
