<flux:main>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('patients.index') }}" wire:navigate>{{ __('Patients') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('patients.show', $patient) }}" wire:navigate>{{ $patient->full_name }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $editing ? __('Edit Encounter') : __('New Encounter') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl" level="1">
        {{ $editing ? __('Edit Encounter') : __('New Encounter') }}
    </flux:heading>

    <form wire:submit="save" class="mt-8 max-w-2xl space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <flux:select wire:model="type" :label="__('Note Type')" required>
                <option value="soap">{{ __('SOAP Note') }}</option>
                <option value="dap">{{ __('DAP Note') }}</option>
                <option value="narrative">{{ __('Narrative') }}</option>
                <option value="intake">{{ __('Intake / Evaluation') }}</option>
                <option value="custom">{{ __('Custom Template') }}</option>
            </flux:select>

            <flux:input wire:model="encounter_date" type="datetime-local" :label="__('Date & Time')" required />
        </div>

        <flux:textarea wire:model="chief_complaint" :label="__('Chief Complaint')" rows="2" />

        <flux:textarea wire:model="assessment" :label="__('Assessment')" rows="6" />

        <flux:textarea wire:model="plan" :label="__('Plan')" rows="4" />

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">
                {{ __('Save Encounter') }}
            </flux:button>

            <flux:button variant="ghost" href="{{ route('patients.show', $patient) }}" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
</flux:main>
