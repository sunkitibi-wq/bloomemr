<flux:main class="space-y-6 h-full flex flex-col">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Flow Board</flux:heading>
            <flux:subheading>Track patient flow through the clinic.</flux:subheading>
        </div>
        
        <div class="mt-4 sm:mt-0 flex items-center space-x-4">
            <flux:input type="date" wire:model.live="date" class="w-48" />
        </div>
    </div>

    <div class="flex overflow-x-auto pb-4 gap-6 items-start h-[calc(100vh-140px)] min-h-[500px]">
        @foreach($boards as $status => $boardAppointments)
            <div class="flex-shrink-0 w-80 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 flex flex-col h-full max-h-full">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between sticky top-0 bg-zinc-50 dark:bg-zinc-900 rounded-t-xl z-10">
                    <h3 class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $status }}</h3>
                    <flux:badge size="sm">{{ $boardAppointments->count() }}</flux:badge>
                </div>
                
                <div class="p-3 overflow-y-auto flex-1 space-y-3">
                    @forelse($boardAppointments as $appointment)
                        <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-medium text-zinc-900 dark:text-white">
                                    <a href="{{ route('patients.show', $appointment->patient) }}" class="hover:underline">
                                        {{ $appointment->patient->full_name }}
                                    </a>
                                </div>
                                <div class="text-sm text-zinc-500">
                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}
                                </div>
                            </div>
                            
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                                Provider: {{ $appointment->provider->name }}
                            </div>
                            
                            <flux:dropdown>
                                <flux:button size="sm" variant="subtle" class="w-full justify-between">
                                    Move to... <flux:icon.chevron-down class="size-4 ml-2" />
                                </flux:button>
                                
                                <flux:menu>
                                    @if($status !== 'Scheduled')
                                        <flux:menu.item wire:click="updateStatus({{ $appointment->id }}, 'scheduled')">Scheduled</flux:menu.item>
                                    @endif
                                    @if($status !== 'Arrived')
                                        <flux:menu.item wire:click="updateStatus({{ $appointment->id }}, 'arrived')">Arrived</flux:menu.item>
                                    @endif
                                    @if($status !== 'Triaged')
                                        <flux:menu.item wire:click="updateStatus({{ $appointment->id }}, 'triaged')">Triaged (Vitals)</flux:menu.item>
                                    @endif
                                    @if($status !== 'In Room')
                                        <flux:menu.item wire:click="updateStatus({{ $appointment->id }}, 'in-room')">In Room</flux:menu.item>
                                    @endif
                                    @if($status !== 'Completed')
                                        <flux:menu.item wire:click="updateStatus({{ $appointment->id }}, 'completed')">Completed / Checkout</flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    @empty
                        <div class="text-center py-6 text-sm text-zinc-500">
                            No patients
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</flux:main>
