<?php

namespace App\Livewire;

use App\Models\AssetMaintenance;
use App\Models\InventoryItem;
use App\Services\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Inventory & Assets')]
class InventoryManager extends Component
{
    public string $activeTab = 'supplies';

    // Supply form
    public string $itemName = '';
    public string $itemSku = '';
    public string $itemCategory = 'medical_supplies';
    public int $stockQuantity = 0;
    public int $reorderLevel = 10;

    // Asset form
    public string $assetName = '';
    public string $serialNumber = '';
    public string $nextCalibrationDue = '';

    // Restock
    public ?int $restockItemId = null;
    public int $restockQuantity = 0;

    public bool $showAddItem = false;
    public bool $showAddAsset = false;
    public bool $showRestockModal = false;

    #[Computed]
    public function inventoryItems()
    {
        return InventoryItem::where('practice_id', Auth::user()->practice_id)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function lowStockItems()
    {
        return InventoryItem::where('practice_id', Auth::user()->practice_id)
            ->whereIn('status', ['low_stock', 'out_of_stock'])
            ->count();
    }

    #[Computed]
    public function assetMaintenances()
    {
        return AssetMaintenance::where('practice_id', Auth::user()->practice_id)
            ->orderBy('next_calibration_due')
            ->get();
    }

    #[Computed]
    public function overdueAssets()
    {
        return AssetMaintenance::where('practice_id', Auth::user()->practice_id)
            ->where('next_calibration_due', '<', now())
            ->count();
    }

    public function addItem(): void
    {
        $this->validate([
            'itemName' => 'required|string|max:255',
            'itemSku' => 'nullable|string|max:100',
            'itemCategory' => 'required|string',
            'stockQuantity' => 'required|integer|min:0',
            'reorderLevel' => 'required|integer|min:0',
        ]);

        InventoryItem::create([
            'practice_id' => Auth::user()->practice_id,
            'name' => $this->itemName,
            'sku' => $this->itemSku ?: null,
            'category' => $this->itemCategory,
            'stock_quantity' => $this->stockQuantity,
            'reorder_level' => $this->reorderLevel,
            'status' => $this->stockQuantity <= $this->reorderLevel ? 'low_stock' : 'active',
        ]);

        $this->reset(['itemName', 'itemSku', 'itemCategory', 'stockQuantity', 'reorderLevel', 'showAddItem']);
        $this->stockQuantity = 0;
        $this->reorderLevel = 10;
        $this->itemCategory = 'medical_supplies';

        session()->flash('success', 'Item added to inventory.');
    }

    public function addAsset(): void
    {
        $this->validate([
            'assetName' => 'required|string|max:255',
            'serialNumber' => 'nullable|string|max:100',
            'nextCalibrationDue' => 'required|date',
        ]);

        AssetMaintenance::create([
            'practice_id' => Auth::user()->practice_id,
            'asset_name' => $this->assetName,
            'serial_number' => $this->serialNumber ?: null,
            'status' => 'operational',
            'next_calibration_due' => $this->nextCalibrationDue,
        ]);

        $this->reset(['assetName', 'serialNumber', 'nextCalibrationDue', 'showAddAsset']);

        session()->flash('success', 'Asset scheduled for maintenance tracking.');
    }

    public function openRestock(int $itemId): void
    {
        $this->restockItemId = $itemId;
        $this->restockQuantity = 0;
        $this->showRestockModal = true;
    }

    public function confirmRestock(InventoryService $service): void
    {
        $this->validate([
            'restockQuantity' => 'required|integer|min:1',
        ]);

        $item = InventoryItem::findOrFail($this->restockItemId);
        $service->restockItem($item, $this->restockQuantity);

        $this->reset(['restockItemId', 'restockQuantity', 'showRestockModal']);

        session()->flash('success', 'Item restocked successfully.');
    }

    public function markAssetServiced(int $assetId, InventoryService $service): void
    {
        $asset = AssetMaintenance::findOrFail($assetId);
        $service->scheduleMaintenance($asset, now()->addYear()->toDateString());

        session()->flash('success', 'Asset marked as serviced. Next calibration scheduled in 12 months.');
    }

    public function render(): View
    {
        return view('livewire.inventory-manager');
    }
}
