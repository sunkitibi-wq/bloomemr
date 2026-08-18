<div class="max-w-7xl mx-auto py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 px-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Vendors</h1>
            <p class="text-sm text-slate-500 mt-1">Manage suppliers and external business partners</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <div class="relative w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon.magnifying-glass class="h-4 w-4 text-slate-400" />
                </div>
                <input wire:model.live="search" type="text" placeholder="Search vendors..." class="block w-full pl-10 pr-3 py-2 border-none bg-white shadow-sm rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 placeholder-slate-400">
            </div>
            <flux:select wire:model.live="filterStatus" class="w-32 bg-white border-none shadow-sm rounded-xl" size="sm">
                <flux:select.option value="all">All Status</flux:select.option>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
            </flux:select>
            <flux:button wire:click="create" variant="primary" icon="plus" class="rounded-xl shadow-sm">Add Vendor</flux:button>
        </div>
    </div>

    <!-- Bento Box Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($vendors as $vendor)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col hover:shadow-md transition-all duration-200 group">
                <!-- Top row: Avatar & Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-lg border border-indigo-100/50">
                        {{ substr($vendor->name, 0, 1) }}
                    </div>
                    @if($vendor->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold tracking-wider uppercase bg-emerald-50 text-emerald-700">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold tracking-wider uppercase bg-slate-100 text-slate-600">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                            Inactive
                        </span>
                    @endif
                </div>

                <!-- Info -->
                <h3 class="text-base font-bold text-slate-800 mb-1 truncate group-hover:text-indigo-700 transition-colors cursor-pointer">{{ $vendor->name }}</h3>
                <p class="text-xs text-slate-500 mb-4 line-clamp-2 min-h-[32px]">{{ $vendor->notes ?: 'No description provided.' }}</p>

                <!-- Contact Details -->
                <div class="space-y-3 flex-1 mt-2 pt-4 border-t border-slate-50">
                    @if($vendor->contact_person)
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-7 h-7 rounded-full bg-slate-50 flex items-center justify-center shrink-0">
                                <flux:icon.user class="w-3.5 h-3.5 text-slate-400" />
                            </div>
                            <span class="text-slate-700 font-medium truncate">{{ $vendor->contact_person }}</span>
                        </div>
                    @endif
                    
                    @if($vendor->email)
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-7 h-7 rounded-full bg-slate-50 flex items-center justify-center shrink-0">
                                <flux:icon.envelope class="w-3.5 h-3.5 text-slate-400" />
                            </div>
                            <a href="mailto:{{ $vendor->email }}" class="text-slate-600 hover:text-indigo-600 truncate">{{ $vendor->email }}</a>
                        </div>
                    @endif

                    @if($vendor->phone)
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-7 h-7 rounded-full bg-slate-50 flex items-center justify-center shrink-0">
                                <flux:icon.phone class="w-3.5 h-3.5 text-slate-400" />
                            </div>
                            <a href="tel:{{ $vendor->phone }}" class="text-slate-600 hover:text-indigo-600">{{ $vendor->phone }}</a>
                        </div>
                    @endif
                </div>
                
                <div class="mt-5 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                        View Profile <flux:icon.chevron-right class="w-3 h-3" stroke-width="3" />
                    </button>
                    <button class="text-slate-400 hover:text-slate-700">
                        <flux:icon.ellipsis-horizontal class="w-5 h-5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <flux:icon.truck class="w-8 h-8 text-slate-300" variant="outline" />
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">No vendors found</h3>
                <p class="text-sm text-slate-500">Try adjusting your search or filters.</p>
            </div>
        @endforelse
    </div>

    <flux:modal name="vendor-modal" wire:model="isModalOpen" class="md:w-[600px]">
        <form wire:submit="save">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-800">{{ $vendorId ? 'Edit Vendor' : 'Add Vendor' }}</h2>
            </div>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="name" label="Vendor Name" placeholder="Enter vendor name" />
                    <flux:input wire:model="contact_person" label="Contact Person" placeholder="Name of primary contact" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="email" label="Email Address" type="email" placeholder="contact@vendor.com" />
                    <flux:input wire:model="phone" label="Phone Number" placeholder="(555) 123-4567" />
                </div>
                
                <flux:select wire:model="status" label="Status">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>

                <flux:textarea wire:model="address" label="Address" placeholder="Physical or mailing address" rows="2" />
                <flux:textarea wire:model="notes" label="Notes" placeholder="Any additional information" rows="3" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save Vendor</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
