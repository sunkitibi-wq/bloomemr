<?php

namespace App\Livewire\Patients;

use App\Actions\LogAudit;
use App\Models\Document;
use App\Models\EligibilityCheck;
use App\Models\Encounter;
use App\Models\FormTemplate;
use App\Models\Patient;
use App\Models\PatientForm;
use App\Services\EligibilityService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Patient Detail')]
class PatientDetail extends Component
{
    public Patient $patient;

    public string $activeTab = 'encounters';

    public ?EligibilityCheck $latestEligibilityCheck = null;

    /** @var array<int, array<string, mixed>> */
    public array $templatesList = [];

    public string $templateTitle = '';

    public string $templateDescription = '';

    public string $templateType = 'consent';

    /** @var array<int, array<string, mixed>> */
    public array $templateFields = [];

    // Modal state for viewing completed forms
    public bool $showFormModal = false;

    public ?array $selectedFormData = null;

    public ?string $selectedFormTitle = null;

    public ?string $selectedFormSignature = null;

    public ?string $selectedFormSignedAt = null;

    public ?string $selectedFormIp = null;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient->load([
            'primaryProvider',
            'encounters' => fn ($q) => $q->latest()->limit(10),
            'documents' => fn ($q) => $q->latest()->limit(10),
            'assessments' => fn ($q) => $q->latest()->limit(10),
            'medications' => fn ($q) => $q->latest()->limit(10),
            'prescriptions' => fn ($q) => $q->latest()->limit(10),
            'labOrders' => fn ($q) => $q->latest()->limit(10),
            'labResults' => fn ($q) => $q->latest()->limit(10),
            'patientForms.template' => fn ($q) => $q->latest(),
            'radiologyOrders.report' => fn ($q) => $q->latest(),
        ]);

        $this->latestEligibilityCheck = EligibilityCheck::where('patient_id', $patient->id)->latest()->first();

        $this->templatesList = FormTemplate::where('is_active', true)->get()->toArray();
        $this->templateFields = [[
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
        ]];

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'view',
            entityType: 'patient',
            entityId: $patient->id,
            patientId: $patient->id,
        );

        $recent = session('recent_patients', []);
        $recent = array_diff($recent, [$patient->id]);
        array_unshift($recent, $patient->id);
        $recent = array_slice($recent, 0, 10);
        session(['recent_patients' => $recent]);
    }

    public function deletePatient(): void
    {
        Gate::authorize('delete', $this->patient);

        $this->patient->delete();

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'delete',
            entityType: 'patient',
            entityId: $this->patient->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: 'Patient record deleted.');
        $this->redirect(route('patients.index'), navigate: true);
    }

    public function releaseEncounterToPortal(Encounter $encounter): void
    {
        Gate::authorize('update', $encounter);

        if ($encounter->status !== 'signed') {
            Flux::toast(variant: 'warning', text: __('Only signed encounters can be released to the portal.'));

            return;
        }

        $encounter->update(['released_to_portal_at' => now()]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'release_to_portal',
            entityType: 'encounter',
            entityId: $encounter->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: __('Visit summary released to patient portal.'));
        $this->patient->load(['encounters' => fn ($q) => $q->latest()->limit(10)]);
    }

    public function deleteEncounter(Encounter $encounter): void
    {
        Gate::authorize('delete', $encounter);

        $encounter->delete();

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'delete',
            entityType: 'encounter',
            entityId: $encounter->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: 'Encounter deleted.');
        $this->patient->load([
            'encounters' => fn ($q) => $q->latest()->limit(10),
        ]);
    }

    public function deleteDocument(Document $document): void
    {
        Gate::authorize('delete', $document);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'delete',
            entityType: 'document',
            entityId: $document->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: 'Document deleted.');
        $this->patient->load([
            'documents' => fn ($q) => $q->latest()->limit(10),
        ]);
    }

    public function addTemplateField(): void
    {
        $this->templateFields[] = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
        ];
    }

    public function removeTemplateField(int $index): void
    {
        if (! isset($this->templateFields[$index])) {
            return;
        }

        unset($this->templateFields[$index]);
        $this->templateFields = array_values($this->templateFields);
    }

    public function createTemplate(): void
    {
        $this->validate([
            'templateTitle' => ['required', 'string', 'min:3', 'max:255'],
            'templateDescription' => ['nullable', 'string', 'max:1000'],
            'templateType' => ['required', 'in:consent,intake'],
            'templateFields' => ['required', 'array', 'min:1'],
            'templateFields.*.name' => ['required', 'string', 'regex:/^[a-z_][a-z0-9_]*$/i'],
            'templateFields.*.label' => ['required', 'string', 'min:2', 'max:255'],
            'templateFields.*.type' => ['required', 'in:text,checkbox,yes_no'],
            'templateFields.*.required' => ['boolean'],
        ]);

        $template = FormTemplate::create([
            'title' => trim($this->templateTitle),
            'description' => $this->templateDescription !== '' ? trim($this->templateDescription) : null,
            'type' => $this->templateType,
            'version' => 1,
            'is_active' => true,
            'fields' => array_values(array_map(function (array $field): array {
                return [
                    'name' => trim($field['name']),
                    'label' => trim($field['label']),
                    'type' => $field['type'],
                    'required' => (bool) ($field['required'] ?? false),
                ];
            }, $this->templateFields)),
        ]);

        $this->templatesList = FormTemplate::where('is_active', true)->get()->toArray();
        $this->resetTemplateBuilder();

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'create',
            entityType: 'form_template',
            entityId: $template->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: __('Form template created.'));
    }

    public function assignForm(int $templateId): void
    {
        $template = FormTemplate::findOrFail($templateId);

        PatientForm::create([
            'patient_id' => $this->patient->id,
            'practice_id' => $this->patient->practice_id,
            'template_id' => $template->id,
            'status' => 'pending',
        ]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'create',
            entityType: 'patient_form',
            entityId: $templateId,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: __('Form assigned to Patient Portal.'));

        $this->patient->load([
            'patientForms.template' => fn ($q) => $q->latest(),
        ]);
    }

    public function viewCompletedForm(int $formId): void
    {
        $form = PatientForm::with('template')->findOrFail($formId);

        $this->selectedFormTitle = $form->template->title;
        $this->selectedFormData = $form->form_data;
        $this->selectedFormSignature = $form->signature_name;
        $this->selectedFormSignedAt = $form->signed_at ? $form->signed_at->format('M j, Y H:i:s') : null;
        $this->selectedFormIp = $form->signature_ip;

        $this->showFormModal = true;
    }

    protected function resetTemplateBuilder(): void
    {
        $this->templateTitle = '';
        $this->templateDescription = '';
        $this->templateType = 'consent';
        $this->templateFields = [[
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
        ]];
    }

    public function deleteForm(int $formId): void
    {
        $form = PatientForm::findOrFail($formId);
        $form->delete();

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'delete',
            entityType: 'patient_form',
            entityId: $formId,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: __('Assigned form removed.'));

        $this->patient->load([
            'patientForms.template' => fn ($q) => $q->latest(),
        ]);
    }

    public function checkInsuranceEligibility(): void
    {
        $service = app(EligibilityService::class);
        $this->latestEligibilityCheck = $service->checkEligibility($this->patient);

        Flux::toast(
            variant: $this->latestEligibilityCheck->status === 'eligible' ? 'success' : 'warning',
            text: __('Insurance verification completed: :status', ['status' => ucfirst($this->latestEligibilityCheck->status)])
        );
    }

    public function render(): View
    {
        return view('livewire.patients.patient-detail');
    }
}
