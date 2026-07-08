<?php

namespace App\Services;

use App\Models\AssetMaintenance;
use App\Models\InventoryItem;
use Carbon\Carbon;

class InventoryService
{
    /**
     * Consume a quantity of an inventory item.
     */
    public function consumeItem(InventoryItem $item, int $quantity): InventoryItem
    {
        $item->stock_quantity = max(0, $item->stock_quantity - $quantity);

        if ($item->stock_quantity === 0) {
            $item->status = 'out_of_stock';
        } elseif ($item->stock_quantity <= $item->reorder_level) {
            $item->status = 'low_stock';
        } else {
            $item->status = 'active';
        }

        $item->save();

        return $item;
    }

    /**
     * Restock an inventory item.
     */
    public function restockItem(InventoryItem $item, int $quantity): InventoryItem
    {
        $item->stock_quantity += $quantity;

        if ($item->stock_quantity > $item->reorder_level) {
            $item->status = 'active';
        } elseif ($item->stock_quantity > 0) {
            $item->status = 'low_stock';
        }

        $item->save();

        return $item;
    }

    /**
     * Record maintenance calibration schedule for clinical assets.
     */
    public function scheduleMaintenance(AssetMaintenance $asset, string $nextDueDate): AssetMaintenance
    {
        $asset->update([
            'status' => 'operational',
            'last_calibrated_at' => now(),
            'next_calibration_due' => Carbon::parse($nextDueDate),
        ]);

        return $asset;
    }
}
