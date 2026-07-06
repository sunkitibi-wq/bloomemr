<flux:main>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('patients.index') }}" wire:navigate>{{ __('Patients') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('patients.show', $patient) }}" wire:navigate>{{ $patient->full_name }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Upload Document') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl" level="1">{{ __('Upload Document') }}</flux:heading>

    <form wire:submit="save" class="mt-8 max-w-xl space-y-6">
        <flux:select wire:model="category" :label="__('Category')" required>
            <option value="outside_records">{{ __('Outside Records') }}</option>
            <option value="iep">{{ __('IEP') }}</option>
            <option value="behavioral_plan">{{ __('Behavioral Plan') }}</option>
            <option value="school_report">{{ __('School Report') }}</option>
            <option value="prior_auth">{{ __('Prior Authorization') }}</option>
            <option value="other">{{ __('Other') }}</option>
        </flux:select>

        <flux:input wire:model="file" type="file" :label="__('File')" accept=".pdf,.docx,.jpeg,.jpg,.png,.tiff" />

        <flux:text class="text-xs text-neutral-500">
            {{ __('Accepted: PDF, DOCX, JPEG, PNG, TIFF (max 100 MB)') }}
        </flux:text>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">
                {{ __('Upload') }}
            </flux:button>

            <flux:button variant="ghost" href="{{ route('patients.show', $patient) }}" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
</flux:main>
