<?php

namespace App\Livewire\Encounters;

use App\Actions\LogAudit;
use App\Models\Encounter;
use App\Models\Patient;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('New Encounter')]
class EncounterForm extends Component
{
    public Patient $patient;

    public ?Encounter $encounter = null;

    public string $type = 'soap';

    public string $status = 'draft';

    public string $encounter_date = '';

    public string $chief_complaint = '';

    public string $assessment = '';

    public string $plan = '';

    public bool $editing = false;

    public function mount(Patient $patient, ?Encounter $encounter = null): void
    {
        $this->patient = $patient;
        $this->encounter = $encounter;
        $this->encounter_date = now()->format('Y-m-d\TH:i');

        if ($encounter) {
            $this->editing = true;
            $this->type = $encounter->type;
            $this->status = $encounter->status;
            $this->encounter_date = $encounter->encounter_date->format('Y-m-d\TH:i');
            $this->chief_complaint = $encounter->chief_complaint ?? '';
            $this->assessment = $encounter->assessment ?? '';
            $this->plan = $encounter->plan ?? '';
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'patient_id' => $this->patient->id,
            'provider_id' => Auth::id(),
            'type' => $this->type,
            'status' => $this->status,
            'encounter_date' => $this->encounter_date,
            'chief_complaint' => $this->chief_complaint ?: null,
            'assessment' => $this->assessment ?: null,
            'plan' => $this->plan ?: null,
        ];

        if ($this->editing) {
            $this->encounter->update($data);

            app(LogAudit::class)(
                user: Auth::user(),
                action: 'update',
                entityType: 'encounter',
                entityId: $this->encounter->id,
                patientId: $this->patient->id,
            );

            Flux::toast(variant: 'success', text: 'Encounter updated.');
        } else {
            $encounter = Encounter::create($data);

            app(LogAudit::class)(
                user: Auth::user(),
                action: 'create',
                entityType: 'encounter',
                entityId: $encounter->id,
                patientId: $this->patient->id,
            );

            Flux::toast(variant: 'success', text: 'Encounter created.');
        }

        $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:soap,dap,narrative,intake,custom'],
            'encounter_date' => ['required', 'date'],
            'chief_complaint' => ['nullable', 'string', 'max:500'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
        ];
    }

    public function render(): View
    {
        return view('livewire.encounters.encounter-form');
    }
}
