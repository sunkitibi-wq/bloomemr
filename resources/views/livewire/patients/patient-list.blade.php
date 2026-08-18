<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Patients</h1>
            <p class="text-[13px] text-slate-500 mt-1">Manage your patient directory and records.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="relative">
                <input wire:model.live.debounce="search" type="text" class="block w-64 pl-10 pr-4 py-2 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] transition-colors shadow-sm" placeholder="Search patients..." />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon.magnifying-glass class="h-4 w-4 text-slate-400" />
                </div>
            </div>
            <a href="{{ route('patients.create') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-xl shadow-sm text-[13px] font-semibold text-white hover:bg-indigo-700 transition-colors">
                <flux:icon.plus class="w-4 h-4" />
                New Patient
            </a>
        </div>
    </div>

    <!-- Patients Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px] whitespace-nowrap">
                <thead class="text-[11px] text-slate-400 font-medium border-b border-slate-100 bg-slate-50/50 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-4 font-medium">MRN</th>
                        <th class="px-5 py-4 font-medium">Patient Name</th>
                        <th class="px-5 py-4 font-medium">Date of Birth</th>
                        <th class="px-5 py-4 font-medium">Contact</th>
                        <th class="px-5 py-4 font-medium">Primary Provider</th>
                        <th class="px-5 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->patients as $patient)
                        @php
                            $initials = strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1));
                            $colors = ['purple', 'blue', 'emerald', 'orange', 'amber'];
                            $color = $colors[crc32($patient->id) % count($colors)];
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-5 py-4 text-slate-500 font-medium font-mono">{{ $patient->mrn }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('patients.show', $patient) }}" wire:navigate class="flex items-center gap-3">
                                    @if ($patient->photo_path)
                                        <img src="{{ Storage::url($patient->photo_path) }}" alt="" class="w-8 h-8 rounded-full object-cover shadow-sm" />
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-{{ $color }}-100 text-{{ $color }}-700 flex items-center justify-center text-[11px] font-bold shadow-sm">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $patient->full_name }}</span>
                                        <span class="text-[11px] text-slate-400">{{ ucfirst($patient->gender->value ?? '') }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $patient->date_of_birth?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600 flex flex-col">
                                <span class="font-medium text-slate-700">{{ $patient->phone }}</span>
                                <span class="text-[11px] text-slate-400">{{ $patient->email }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                @if($patient->primaryProvider)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200">
                                        <flux:icon.user-circle class="w-3.5 h-3.5 text-slate-400" />
                                        {{ $patient->primaryProvider->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[12px] italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('patients.show', $patient) }}" wire:navigate class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors">
                                    <flux:icon.arrow-right class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center opacity-60">
                                    <flux:icon.users class="w-12 h-12 text-slate-300 mb-3" variant="outline" />
                                    <p class="text-slate-500 font-medium">No patients found.</p>
                                    <p class="text-[12px] text-slate-400 mt-1">Try adjusting your search or create a new patient.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($this->patients->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $this->patients->links() }}
            </div>
        @endif
    </div>
</div>
