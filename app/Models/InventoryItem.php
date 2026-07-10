<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use BelongsToPractice, HasFactory;

    protected $fillable = [
        'practice_id',
        'name',
        'sku',
        'lot_number',
        'expiration_date',
        'category',
        'stock_quantity',
        'reorder_level',
        'status',
    ];
}
