<flux:main class="space-y-8 bg-[#f7f9fb] dark:bg-zinc-950 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="text-trust-navy dark:text-zinc-100 font-bold tracking-tight">
                {{ __('Inventory & Asset Management') }}
            </flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('Track medical supplies, vaccine stocks, and equipment calibration schedules.') }}
            </flux:text>
        </div>
        <div class="flex items-center gap-2">
            <flux:button wire:click="$set('showAddAsset', true)" variant="outline">
                <flux:icon.wrench-screwdriver class="size-4 mr-1.5" />
                {{ __('Add Asset') }}
            </flux:button>
            <flux:button wire:click="$set('showAddItem', true)" variant="primary">
                <flux:icon.plus class="size-4 mr-1.5" />
                {{ __('Add Supply') }}
            </flux:button>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50/70 dark:bg-green-950/20 dark:border-green-800 p-4">
            <flux:icon.check-circle class="size-5 text-green-600 shrink-0" />
            <span class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-primary/5 dark:bg-primary/10 text-primary rounded-xl">
                <flux:icon.archive-box class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Total Items') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->inventoryItems->count() }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/30 text-amber-600 rounded-xl">
                <flux:icon.exclamation-triangle class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Low / Out of Stock') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->lowStockItems }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-red-50 dark:bg-red-950/30 text-red-600 rounded-xl">
                <flux:icon.clock class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Overdue Calibrations') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->overdueAssets }}</div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-1 bg-zinc-100 dark:bg-zinc-800/60 p-1 rounded-xl w-fit">
        <button
            wire:click="$set('activeTab', 'supplies')"
            class="{{ $activeTab === 'supplies' ? 'bg-white dark:bg-zinc-900 shadow-sm text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }} px-4 py-2 text-sm font-medium rounded-lg transition-all"
        >
            {{ __('Supply Inventory') }}
        </button>
        <button
            wire:click="$set('activeTab', 'assets')"
            class="{{ $activeTab === 'assets' ? 'bg-white dark:bg-zinc-900 shadow-sm text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }} px-4 py-2 text-sm font-medium rounded-lg transition-all"
        >
            {{ __('Equipment & Assets') }}
        </button>
    </div>

    <!-- Supplies Tab -->
    @if ($activeTab === 'supplies')
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xs overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">{{ __('Item Name') }}</th>
                        <th class="px-6 py-3.5">{{ __('Category') }}</th>
                        <th class="px-6 py-3.5">{{ __('SKU') }}</th>
                        <th class="px-6 py-3.5 text-center">{{ __('Stock') }}</th>
                        <th class="px-6 py-3.5 text-center">{{ __('Reorder At') }}</th>
                        <th class="px-6 py-3.5">{{ __('Status') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($this->inventoryItems as $item)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors" wire:key="inv-{{ $item->id }}">
                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-zinc-500 capitalize">{{ str_replace('_', ' ', $item->category) }}</td>
                            <td class="px-6 py-4 text-zinc-400 font-mono text-xs">{{ $item->sku ?? '—' }}</td>
                            <td class="px-6 py-4 text-center font-mono font-bold text-zinc-900 dark:text-zinc-100">{{ $item->stock_quantity }}</td>
                            <td class="px-6 py-4 text-center font-mono text-zinc-400">{{ $item->reorder_level }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = ['active' => 'green', 'low_stock' => 'amber', 'out_of_stock' => 'red', 'discontinued' => 'zinc'];
                                    $statusColor = $statusColors[$item->status] ?? 'zinc';
                                @endphp
                                <flux:badge size="sm" color="{{ $statusColor }}">
                                    {{ ucwords(str_replace('_', ' ', $item->status)) }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:button size="sm" variant="ghost" wire:click="openRestock({{ $item->id }})">
                                    {{ __('Restock') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-400 italic text-sm">
                                {{ __('No inventory items found. Add supplies to begin tracking.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Assets Tab -->
    @if ($activeTab === 'assets')
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xs overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">{{ __('Asset Name') }}</th>
                        <th class="px-6 py-3.5">{{ __('Serial Number') }}</th>
                        <th class="px-6 py-3.5">{{ __('Status') }}</th>
                        <th class="px-6 py-3.5">{{ __('Last Serviced') }}</th>
                        <th class="px-6 py-3.5">{{ __('Next Calibration') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($this->assetMaintenances as $asset)
                        @php
                            $isOverdue = $asset->next_calibration_due && $asset->next_calibration_due->isPast();
                        @endphp
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors {{ $isOverdue ? 'bg-red-50/40 dark:bg-red-950/10' : '' }}" wire:key="asset-{{ $asset->id }}">
                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $asset->asset_name }}
                                @if ($isOverdue)
                                    <flux:badge size="sm" color="red" class="ml-2">{{ __('Overdue') }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-400 font-mono text-xs">{{ $asset->serial_number ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $assetColors = ['operational' => 'green', 'under_maintenance' => 'amber', 'decommissioned' => 'red'];
                                    $assetColor = $assetColors[$asset->status] ?? 'zinc';
                                @endphp
                                <flux:badge size="sm" color="{{ $assetColor }}">
                                    {{ ucwords(str_replace('_', ' ', $asset->status)) }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-zinc-500 text-xs">
                                {{ $asset->last_calibrated_at ? $asset->last_calibrated_at->format('M j, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs {{ $isOverdue ? 'text-red-500 font-bold' : 'text-zinc-500' }}">
                                {{ $asset->next_calibration_due ? $asset->next_calibration_due->format('M j, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:button size="sm" variant="ghost" wire:click="markAssetServiced({{ $asset->id }})">
                                    {{ __('Mark Serviced') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-zinc-400 italic text-sm">
                                {{ __('No clinical assets tracked. Add equipment to monitor calibration schedules.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Add Supply Modal -->
    <div x-data="{ open: @entangle('showAddItem') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
                <flux:heading size="lg">{{ __('Add Inventory Item') }}</flux:heading>
                <form wire:submit.prevent="addItem" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Item Name') }}</flux:label>
                        <flux:input wire:model="itemName" placeholder="e.g. Flu Vaccine (QIV)" required />
                        @error('itemName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('SKU / Lot Number') }}</flux:label>
                            <flux:input wire:model="itemSku" placeholder="Optional" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Category') }}</flux:label>
                            <flux:select wire:model="itemCategory">
                                <option value="medical_supplies">{{ __('Medical Supplies') }}</option>
                                <option value="vaccines">{{ __('Vaccines') }}</option>
                                <option value="medications">{{ __('Medications') }}</option>
                                <option value="ppe">{{ __('PPE') }}</option>
                                <option value="office_supplies">{{ __('Office Supplies') }}</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Current Stock') }}</flux:label>
                            <flux:input type="number" wire:model="stockQuantity" min="0" required />
                            @error('stockQuantity') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Reorder Level') }}</flux:label>
                            <flux:input type="number" wire:model="reorderLevel" min="0" required />
                            @error('reorderLevel') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </flux:field>
                    </div>
                    <div class="flex justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button type="button" wire:click="$set('showAddItem', false)">{{ __('Cancel') }}</flux:button>
                        <flux:button type="submit" variant="primary">{{ __('Add Item') }}</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Asset Modal -->
    <div x-data="{ open: @entangle('showAddAsset') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
                <flux:heading size="lg">{{ __('Add Clinical Asset') }}</flux:heading>
                <form wire:submit.prevent="addAsset" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Asset Name') }}</flux:label>
                        <flux:input wire:model="assetName" placeholder="e.g. MRI Scanner — Suite 2" required />
                        @error('assetName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Serial Number') }}</flux:label>
                        <flux:input wire:model="serialNumber" placeholder="Optional" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Next Calibration Due') }}</flux:label>
                        <flux:input type="date" wire:model="nextCalibrationDue" required />
                        @error('nextCalibrationDue') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>
                    <div class="flex justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button type="button" wire:click="$set('showAddAsset', false)">{{ __('Cancel') }}</flux:button>
                        <flux:button type="submit" variant="primary">{{ __('Add Asset') }}</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Restock Modal -->
    <div x-data="{ open: @entangle('showRestockModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
                <flux:heading size="lg">{{ __('Restock Item') }}</flux:heading>
                <form wire:submit.prevent="confirmRestock" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Quantity to Add') }}</flux:label>
                        <flux:input type="number" wire:model="restockQuantity" min="1" required />
                        @error('restockQuantity') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>
                    <div class="flex justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button type="button" wire:click="$set('showRestockModal', false)">{{ __('Cancel') }}</flux:button>
                        <flux:button type="submit" variant="primary">{{ __('Confirm Restock') }}</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</flux:main>
