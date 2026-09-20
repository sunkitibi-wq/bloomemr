<div>
    <!-- Facility -->
    <div>
        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3 px-2">Facility</div>
        <flux:select wire:model.live="facilityId" size="sm" class="w-full bg-white border-none shadow-sm rounded-lg" variant="default" placeholder="Select Facility">
            <x-slot:icon>
                <flux:icon.building-office-2 class="w-4 h-4 text-slate-400 ml-1" />
            </x-slot:icon>
            @foreach($facilities as $facility)
                <flux:select.option value="{{ $facility->id }}">{{ $facility->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <!-- Department -->
    @if($departments->count() > 0)
    <div class="mt-4">
        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3 px-2">Department</div>
        <flux:select wire:model.live="departmentId" size="sm" class="w-full bg-white border-none shadow-sm rounded-lg" variant="default" placeholder="Select Department">
            <x-slot:icon>
                <flux:icon.building-office class="w-4 h-4 text-slate-400 ml-1" />
            </x-slot:icon>
            @foreach($departments as $department)
                <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>
    @endif
</div>
