<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Scheduling & Appointments') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('Request and view patient consultations') }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1">
            @if ($patient)
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-slate-800 text-base font-semibold">
                        {{ __('All Appointments for :name', ['name' => $patient->full_name]) }}
                    </h3>
                    <flux:button variant="primary" icon="calendar" wire:click="openRequestForm">
                        {{ __('Schedule Appointment') }}
                    </flux:button>
                </div>

                @if (session()->has('message'))
                    <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Appointments List -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                    @forelse ($appointments as $appt)
                        <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 flex flex-col items-center justify-center text-slate-800 ">
                                    <span class="text-[9px] font-bold uppercase">{{ $appt->appointment_date->format('M') }}</span>
                                    <span class="text-lg font-bold leading-tight">{{ $appt->appointment_date->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 font-medium">
                                        {{ $appt->appointment_date->format('l') }}, {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($appt->end_time)->format('g:i A') }}
                                    </p>
                                    <p class="text-sm font-semibold text-slate-800 ">
                                        {{ __('Consultation Session with :provider', ['provider' => $appt->provider->name]) }}
                                    </p>
                                    @if ($appt->notes)
                                        <p class="text-xs text-slate-500 mt-1 italic">"{{ $appt->notes }}"</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $statusColors = [
                                        'scheduled' => 'bg-amber-100 text-amber-800 ',
                                        'checked_in' => 'bg-blue-100 text-blue-800 ',
                                        'completed' => 'bg-green-100 text-green-800 ',
                                        'cancelled' => 'bg-slate-100 text-slate-800 ',
                                        'no_show' => 'bg-red-100 text-red-800 ',
                                    ];
                                    $color = $statusColors[$appt->status] ?? 'bg-slate-100 text-slate-800';
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $color }}">
                                    {{ __(ucfirst(str_replace('_', ' ', $appt->status))) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 py-8 italic text-center">{{ __('No appointments scheduled yet.') }}</p>
                    @endforelse
                </div>
            @else
                <div class="p-8 text-center text-slate-500 italic">
                    {{ __('No patient records found.') }}
                </div>
            @endif
    </div>
    <!-- Request Appointment Form Modal -->
    <flux:modal name="portal-appointment-modal" wire:model="isFormOpen" class="max-w-md">
        <form wire:submit="save" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Schedule Appointment') }}</flux:heading>
                <flux:subheading>{{ __('Configure date, time, and notes for the session') }}</flux:subheading>
            </div>

            <flux:select wire:model="providerId" :label="__('Preferred Clinician')" required>
                <option value="">{{ __('Select Clinician...') }}</option>
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

            <flux:textarea wire:model="notes" :label="__('Reason for Appointment / Notes')" rows="3" placeholder="{{ __('Please outline your main concerns or reasons for scheduling...') }}" />
            <flux:error name="notes" />

            <div class="flex justify-end space-x-2 pt-4">
                <flux:button variant="ghost" wire:click="$set('isFormOpen', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Book Slot') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
