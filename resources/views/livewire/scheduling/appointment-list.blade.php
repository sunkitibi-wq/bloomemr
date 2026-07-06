<flux:main>
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Scheduling & Appointments') }}</flux:heading>
            <flux:subheading>{{ __('Manage patient appointments and daily schedules') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openCreateForm">
            {{ __('New Appointment') }}
        </flux:button>
    </div>

    <!-- Alert / Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters Panel -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
        <flux:input type="date" wire:model.live="filterDate" :label="__('Date')" />
        
        <flux:select wire:model.live="filterProvider" :label="__('Provider')">
            <option value="">{{ __('All Providers') }}</option>
            @foreach ($providers as $prov)
                <option value="{{ $prov->id }}">{{ $prov->name }}</option>
            @endforeach
        </flux:select>

        <flux:input type="search" wire:model.live="filterPatient" placeholder="{{ __('Search patient name or MRN...') }}" :label="__('Patient Search')" />
    </div>

    <!-- Appointments Table / Card Grid -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Patient') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Provider') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Notes') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($appointments as $appt)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            {{ \Carbon\Carbon::parse($appt->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appt->end_time)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                            <a href="{{ route('patients.show', $appt->patient) }}" class="hover:underline font-semibold text-primary">
                                {{ $appt->patient->full_name }}
                            </a>
                            <span class="text-xs text-zinc-400 block font-mono">{{ $appt->patient->mrn }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 font-medium">
                            {{ $appt->provider->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $statusColors = [
                                    'scheduled' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                    'checked_in' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'cancelled' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800/70 dark:text-zinc-400',
                                    'no_show' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                ];
                                $color = $statusColors[$appt->status] ?? 'bg-zinc-100 text-zinc-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $color }}">
                                {{ __(ucfirst(str_replace('_', ' ', $appt->status))) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400 max-w-xs truncate">
                            {{ $appt->notes ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-1">
                            @if ($appt->status === 'scheduled')
                                <flux:button size="sm" variant="outline" wire:click="updateStatus({{ $appt->id }}, 'checked_in')">
                                    {{ __('Check In') }}
                                </flux:button>
                            @endif

                            @if (in_array($appt->status, ['scheduled', 'checked_in']))
                                <flux:button size="sm" variant="outline" class="text-green-600 dark:text-green-400" wire:click="updateStatus({{ $appt->id }}, 'completed')">
                                    {{ __('Complete') }}
                                </flux:button>
                            @endif

                            <flux:button size="sm" variant="ghost" icon="pencil" wire:click="edit({{ $appt->id }})" />
                            <flux:button size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" wire:confirm="{{ __('Are you sure you want to delete this appointment?') }}" wire:click="delete({{ $appt->id }})" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-zinc-400">
                            {{ __('No appointments found for this day.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if ($appointments->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

    <!-- Appointment Form Modal / Sliding Side-panel -->
    <flux:modal name="appointment-form-modal" wire:model="isFormOpen" class="max-w-md">
        <form wire:submit="save" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $appointmentId ? __('Edit Appointment') : __('New Appointment') }}</flux:heading>
                <flux:subheading>{{ __('Fill out the details to schedule the slot') }}</flux:subheading>
            </div>

            <flux:select wire:model="patientId" :label="__('Patient')" required>
                <option value="">{{ __('Select Patient...') }}</option>
                @foreach ($patients as $pat)
                    <option value="{{ $pat->id }}">{{ $pat->last_name }}, {{ $pat->first_name }} ({{ $pat->mrn }})</option>
                @endforeach
            </flux:select>
            <flux:error name="patientId" />

            <flux:select wire:model="providerId" :label="__('Provider')" required>
                <option value="">{{ __('Select Provider...') }}</option>
                @foreach ($providers as $prov)
                    <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                @endforeach
            </flux:select>
            <flux:error name="providerId" />

            <flux:input type="date" wire:model="appointmentDate" :label="__('Date')" required />
            <flux:error name="appointmentDate" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input type="time" wire:model="startTime" :label="__('Start Time')" required />
                <flux:input type="time" wire:model="endTime" :label="__('End Time')" required />
            </div>
            <flux:error name="startTime" />
            <flux:error name="endTime" />

            <flux:select wire:model="status" :label="__('Status')" required>
                <option value="scheduled">{{ __('Scheduled') }}</option>
                <option value="checked_in">{{ __('Checked In') }}</option>
                <option value="completed">{{ __('Completed') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
                <option value="no_show">{{ __('No Show') }}</option>
            </flux:select>
            <flux:error name="status" />

            <flux:textarea wire:model="notes" :label="__('Notes')" rows="3" placeholder="{{ __('Reason for visit, room #, etc.') }}" />
            <flux:error name="notes" />

            <div class="flex justify-end space-x-2 pt-4">
                <flux:button variant="ghost" wire:click="$set('isFormOpen', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</flux:main>
