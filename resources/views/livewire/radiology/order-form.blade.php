<form wire:submit="save" class="space-y-6">
    <flux:select wire:model="patient_id" label="{{ __('Patient') }}" placeholder="Choose a patient...">
        @foreach($patients as $patient)
            <flux:select.option value="{{ $patient->id }}">{{ $patient->full_name }} (DOB: {{ $patient->date_of_birth->format('m/d/Y') }})</flux:select.option>
        @endforeach
    </flux:select>

    <flux:input wire:model="procedure_name" label="{{ __('Procedure Name') }}" placeholder="e.g., MRI Brain w/o Contrast" />
    
    <flux:textarea wire:model="clinical_indication" label="{{ __('Clinical Indication') }}" placeholder="Reason for the scan, symptoms, relevant history..." rows="4" />

    <div class="flex justify-end gap-2">
        <flux:modal.close>
            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
        </flux:modal.close>
        <flux:button type="submit" variant="primary">{{ __('Create Order') }}</flux:button>
    </div>
</form>
