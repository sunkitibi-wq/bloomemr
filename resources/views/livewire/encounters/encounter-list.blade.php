<div class="max-w-[1600px] w-full mx-auto py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 px-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Clinical Notes</h1>
            <p class="text-sm text-slate-500 mt-1">Manage all patient encounters and clinical documentation</p>
        </div>
        <div class="flex items-center gap-3">
            <flux:select wire:model.live="filterStatus" class="w-48 bg-white border-none shadow-sm rounded-lg" size="sm">
                <flux:select.option value="all">All Notes</flux:select.option>
                <flux:select.option value="draft">Drafts</flux:select.option>
                <flux:select.option value="co_sign_pending">Pending Co-Sign</flux:select.option>
                <flux:select.option value="signed">Signed</flux:select.option>
            </flux:select>
            <flux:button variant="primary" icon="plus" wire:click="openCreateModal" class="rounded-xl shadow-sm">New Note</flux:button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px] whitespace-nowrap">
                <thead class="text-[11px] text-slate-400 font-medium border-b border-slate-100 bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium">Patient</th>
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">Provider</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($encounters as $encounter)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-slate-600 font-medium">{{ $encounter->encounter_date ? $encounter->encounter_date->format('M j, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('patients.show', $encounter->patient) }}" wire:navigate class="font-bold text-slate-700 hover:text-indigo-600 transition-colors">
                                    {{ $encounter->patient->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ ucfirst(str_replace('_', ' ', $encounter->type)) }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $encounter->provider->name }}</td>
                            <td class="px-6 py-4">
                                @if($encounter->status === 'signed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        <flux:icon.check-circle class="w-3.5 h-3.5" variant="solid" />
                                        Signed
                                    </span>
                                @elseif($encounter->status === 'co_sign_pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-orange-50 text-orange-700">
                                        <flux:icon.clock class="w-3.5 h-3.5" variant="solid" />
                                        Pending Co-Sign
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                        <flux:icon.document-text class="w-3.5 h-3.5" variant="solid" />
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:button size="sm" variant="ghost" href="{{ route('encounters.note', $encounter) }}" wire:navigate>
                                    View Note
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <flux:icon.document-text class="w-6 h-6 text-slate-400" variant="outline" />
                                </div>
                                <h3 class="text-base font-bold text-slate-800 mb-1">No notes found</h3>
                                <p class="text-sm text-slate-500">There are no clinical notes matching the current filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($encounters->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $encounters->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <flux:modal name="create-encounter-modal" wire:model="isCreateModalOpen" class="max-w-md">
        <form wire:submit="proceedToCreate">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-800">Create New Note</h2>
                <p class="text-sm text-slate-500">Select a patient to document a new clinical encounter.</p>
            </div>
            
            <div class="space-y-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Patient</label>
                <select wire:model="selectedPatientId" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Choose a patient...</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }} (DOB: {{ $patient->date_of_birth ? $patient->date_of_birth->format('m/d/Y') : 'N/A' }})</option>
                    @endforeach
                </select>
                @error('selectedPatientId') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Continue</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
