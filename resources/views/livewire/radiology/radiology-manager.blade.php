<flux:main class="space-y-6">
    <header class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Radiology Management') }}</flux:heading>
            <flux:subheading>{{ __('Manage patient imaging orders and reports.') }}</flux:subheading>
        </div>
        
        <flux:modal.trigger name="create-order-modal">
            <flux:button variant="primary" icon="plus">{{ __('New Order') }}</flux:button>
        </flux:modal.trigger>
    </header>

    <div class="flex items-center gap-4">
        <div class="flex-1 max-w-sm">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search by patient name..." />
        </div>
        <div class="w-48">
            <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                <flux:select.option value="">All Statuses</flux:select.option>
                <flux:select.option value="ordered">Ordered</flux:select.option>
                <flux:select.option value="completed">Completed</flux:select.option>
                <flux:select.option value="reported">Reported</flux:select.option>
            </flux:select>
        </div>
    </div>

    <flux:card class="overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Order Date') }}</flux:table.column>
                <flux:table.column>{{ __('Patient') }}</flux:table.column>
                <flux:table.column>{{ __('Procedure') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Ordered By') }}</flux:table.column>
                <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($orders as $order)
                    <flux:table.row :key="$order->id">
                        <flux:table.cell>
                            <span class="text-sm">{{ $order->order_date->format('M j, Y') }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:avatar :name="$order->patient->full_name" size="sm" />
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $order->patient->full_name }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-sm font-medium">{{ $order->procedure_name }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                :color="match($order->status) {
                                    'ordered' => 'yellow',
                                    'completed' => 'blue',
                                    'reported' => 'green',
                                    default => 'zinc',
                                }"
                            >
                                {{ ucfirst($order->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm">{{ $order->orderedBy->name }}</span>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:dropdown position="bottom-end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                <flux:menu>
                                    @if ($order->status === 'ordered')
                                        <flux:menu.item wire:click="$set('orderIdToComplete', {{ $order->id }})" icon="check-circle">{{ __('Mark Completed') }}</flux:menu.item>
                                    @endif
                                    @if (in_array($order->status, ['ordered', 'completed']))
                                        <flux:modal.trigger name="upload-report-modal-{{ $order->id }}">
                                            <flux:menu.item icon="document-arrow-up">{{ __('Upload Report') }}</flux:menu.item>
                                        </flux:modal.trigger>
                                    @endif
                                    @if ($order->status === 'reported')
                                        <flux:menu.item icon="document-magnifying-glass" :href="route('patients.show', $order->patient_id)">{{ __('View in Chart') }}</flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                    
                    <flux:modal name="upload-report-modal-{{ $order->id }}" class="md:w-3/4 lg:w-2/3">
                        <div class="mb-4">
                            <flux:heading size="lg">{{ __('Upload Radiology Report') }}</flux:heading>
                            <flux:subheading>{{ __('Attach findings for :procedure', ['procedure' => $order->procedure_name]) }}</flux:subheading>
                        </div>
                        
                        <livewire:radiology.report-upload :order="$order" :key="'upload-'.$order->id" />
                    </flux:modal>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">
                            {{ __('No radiology orders found.') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        
        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $orders->links() }}
            </div>
        @endif
    </flux:card>
    
    <flux:modal name="create-order-modal" class="md:w-[600px]">
        <div class="mb-4">
            <flux:heading size="lg">{{ __('New Radiology Order') }}</flux:heading>
            <flux:subheading>{{ __('Order a new imaging procedure for a patient.') }}</flux:subheading>
        </div>
        <livewire:radiology.order-form />
    </flux:modal>
</flux:main>
