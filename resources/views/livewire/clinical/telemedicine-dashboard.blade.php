<div class="max-w-[1600px] w-full mx-auto py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 px-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Telemedicine Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">Manage today's telehealth sessions and connect with your patients</p>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="primary" icon="video-camera" wire:click="openCreateModal" class="rounded-xl shadow-sm">Start Session</flux:button>
        </div>
    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- Today's Appointments -->
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-bold text-[14px] text-slate-800 mb-5 flex items-center gap-2">
                <flux:icon.calendar class="w-4 h-4 text-slate-400" /> Today's Telehealth Appointments
            </h3>
            
            <div class="flex flex-col gap-3">
                @forelse($appointments as $appointment)
                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center shrink-0">
                                <flux:icon.video-camera class="w-5 h-5 text-indigo-500" variant="outline" />
                            </div>
                            <div>
                                <a href="{{ route('patients.show', $appointment->patient) }}" wire:navigate class="text-[14px] font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                                    {{ $appointment->patient->full_name }}
                                </a>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[12px] text-slate-500 font-medium">{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}</span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="text-[12px] text-slate-500">{{ $appointment->appointmentType->name ?? 'Telehealth Visit' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <flux:button size="sm" variant="primary" href="{{ route('patients.show', ['patient' => $appointment->patient, 'tab' => 'telehealth']) }}" wire:navigate>Join Call</flux:button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center border border-dashed border-slate-200 rounded-xl bg-slate-50/50 h-full flex flex-col items-center justify-center">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 border border-slate-100 shadow-sm">
                            <flux:icon.check-circle class="w-6 h-6 text-emerald-500" variant="solid" />
                        </div>
                        <h3 class="text-base font-bold text-slate-800 mb-1">No upcoming sessions</h3>
                        <p class="text-sm text-slate-500 max-w-sm">You have no telehealth appointments scheduled for today. Start an ad-hoc session if needed.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column / Quick Actions -->
        <div class="flex flex-col gap-6">
            <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-6">
                <h3 class="font-bold text-[14px] text-indigo-900 mb-2">Telehealth Best Practices</h3>
                <ul class="space-y-3 mt-4">
                    <li class="flex items-start gap-2.5 text-[13px] text-indigo-800">
                        <flux:icon.check-circle class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
                        <span>Ensure your camera and microphone are properly connected before joining a room.</span>
                    </li>
                    <li class="flex items-start gap-2.5 text-[13px] text-indigo-800">
                        <flux:icon.check-circle class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
                        <span>Have the patient's chart open on a separate monitor if possible.</span>
                    </li>
                    <li class="flex items-start gap-2.5 text-[13px] text-indigo-800">
                        <flux:icon.check-circle class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
                        <span>Record consent for the telehealth visit in the clinical note.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Start Ad-hoc Session Modal -->
    <flux:modal name="start-telehealth-modal" wire:model="isCreateModalOpen" class="max-w-md">
        <form wire:submit="startSession">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-800">Start Telehealth Session</h2>
                <p class="text-sm text-slate-500">Select a patient to launch a telehealth room with them.</p>
            </div>
            
            <div class="space-y-4">
                <flux:select wire:model="selectedPatientId" label="Patient" placeholder="Choose a patient...">
                    @foreach($patients as $patient)
                        <flux:select.option value="{{ (string) $patient->id }}">{{ $patient->full_name }} (DOB: {{ $patient->date_of_birth ? $patient->date_of_birth->format('m/d/Y') : 'N/A' }})</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Launch Room</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
