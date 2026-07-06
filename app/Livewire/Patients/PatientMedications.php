<?php

namespace App\Livewire\Patients;

use App\Actions\LogAudit;
use App\Models\Medication;
use App\Models\MedicationTeachingLog;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\Prescription;
use App\Services\ClinicalDecisionSupportService;
use App\Services\SurescriptsService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientMedications extends Component
{
    public Patient $patient;

    // Prescribing fields
    public string $name = '';

    public string $dose = '';

    public string $frequency = '';

    public bool $is_controlled = false;

    public string $ndc_code = '';

    // Clinical warnings
    public ?string $allergyAlert = null;

    public array $ddiAlerts = [];

    // Surescripts formulary details
    public array $formularyDetails = [];

    // Lexicomp teaching sheets
    public array $teachingSheets = [
        'Lexapro Patient Guide',
        'Adderall Parent Information Sheet',
        'Vyvanse Treatment Agreement & Guide',
        'Stimulant Vitals Tracking Diary',
        'Depression Care Plan & Resources',
    ];

    // EPCS 2FA modal
    public bool $showEpcsModal = false;

    public string $two_factor_code = '';

    public ?int $pharmacyId = null;

    // Pharmacy reconciliation list
    public array $externalMeds = [
        ['name' => 'Abilify', 'dose' => '5mg', 'frequency' => 'Daily at bedtime', 'ndc_code' => '59148-008-13', 'source' => 'Patient-Reported'],
        ['name' => 'Ibuprofen', 'dose' => '400mg', 'frequency' => 'As needed for pain', 'ndc_code' => '00406-0397-01', 'source' => 'Surescripts Pharmacy History'],
    ];

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
        $this->patient->load(['medicationTeachingLogs.giver']);
    }

    /** @return \Illuminate\Support\Collection<int, Pharmacy> */
    public function getPharmaciesProperty()
    {
        return Pharmacy::where('practice_id', $this->patient->practice_id ?? Auth::user()->practice_id)
            ->orderBy('name')
            ->get();
    }

    public function updatedName(): void
    {
        $this->runInteractionChecks();

        if ($this->name) {
            $surescripts = app(SurescriptsService::class);
            $this->formularyDetails = $surescripts->checkFormulary($this->patient, $this->name);
        } else {
            $this->formularyDetails = [];
        }
    }

    protected function runInteractionChecks(): void
    {
        $cds = app(ClinicalDecisionSupportService::class);
        $this->allergyAlert = $cds->checkAllergies($this->patient, $this->name);
        $this->ddiAlerts = $cds->checkDrugInteractions($this->patient, $this->name);
    }

    public function prescribe(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'dose' => 'required|string|max:50',
            'frequency' => 'required|string|max:50',
            'is_controlled' => 'boolean',
            'ndc_code' => 'nullable|string|max:50',
        ]);

        // EPCS controlled substance security verification
        if ($this->is_controlled) {
            $this->two_factor_code = '';
            $this->showEpcsModal = true;

            return;
        }

        $this->savePrescription();
    }

    public function signPrescription(): void
    {
        $this->validate([
            'two_factor_code' => 'required|numeric|digits:6',
        ]);

        $this->showEpcsModal = false;
        $this->savePrescription();
    }

    protected function savePrescription(): void
    {
        $med = Medication::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $this->patient->id,
            'name' => $this->name,
            'dose' => $this->dose,
            'frequency' => $this->frequency,
            'prescriber_id' => Auth::id(),
            'status' => 'active',
            'ndc_code' => $this->ndc_code ?: null,
        ]);

        $prescription = Prescription::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $this->patient->id,
            'medication_id' => $med->id,
            'pharmacy_id' => $this->pharmacyId,
            'epcs_id' => $this->is_controlled ? 'EPCS-'.rand(100000, 999999) : null,
            'status' => 'sent',
            'is_controlled' => $this->is_controlled,
            'sent_at' => now(),
        ]);

        // Transmit prescription to Surescripts
        app(SurescriptsService::class)->transmitPrescription($prescription);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'prescribe',
            entityType: 'prescription',
            entityId: $prescription->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: "Prescription for {$this->name} sent successfully.");

        $this->reset(['name', 'dose', 'frequency', 'is_controlled', 'ndc_code', 'pharmacyId', 'allergyAlert', 'ddiAlerts', 'formularyDetails']);
        $this->patient->load(['medications', 'medicationTeachingLogs.giver']);
    }

    public function updatePrescriptionStatus(int $prescriptionId, string $status): void
    {
        $prescription = Prescription::findOrFail($prescriptionId);

        if ($prescription->patient_id !== $this->patient->id) {
            abort(403);
        }

        $prescription->update([
            'fulfillment_status' => $status,
            'status' => $status === 'filled' ? 'filled' : $prescription->status,
            'dispensed_at' => $status === 'filled' ? now() : $prescription->dispensed_at,
        ]);

        Flux::toast(variant: 'success', text: __('Prescription status updated.'));
        $this->patient->load('prescriptions');
    }

    public function assignTeachingSheet(string $sheetTitle, string $medName): void
    {
        MedicationTeachingLog::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $this->patient->id,
            'medication_name' => $medName ?: 'General',
            'material_title' => $sheetTitle,
            'given_by' => Auth::id(),
            'status' => 'assigned',
        ]);

        Flux::toast(variant: 'success', text: "Assigned education material: {$sheetTitle}");
        $this->patient->load(['medicationTeachingLogs.giver']);
    }

    public function reconcileMed(int $index): void
    {
        $extMed = $this->externalMeds[$index];

        $med = Medication::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $this->patient->id,
            'name' => $extMed['name'],
            'dose' => $extMed['dose'],
            'frequency' => $extMed['frequency'],
            'prescriber_id' => Auth::id(),
            'status' => 'active',
            'ndc_code' => $extMed['ndc_code'],
        ]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'reconcile',
            entityType: 'medication',
            entityId: $med->id,
            patientId: $this->patient->id,
        );

        // Remove from external list
        unset($this->externalMeds[$index]);
        $this->externalMeds = array_values($this->externalMeds);

        Flux::toast(variant: 'success', text: "Reconciled {$extMed['name']} into EMR active meds list.");
        $this->patient->load('medications');
    }

    public function dismissExternalMed(int $index): void
    {
        unset($this->externalMeds[$index]);
        $this->externalMeds = array_values($this->externalMeds);
        Flux::toast(text: 'Pharmacy record dismissed.');
    }

    public function discontinue(Medication $medication): void
    {
        $medication->update(['status' => 'discontinued']);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'discontinue',
            entityType: 'medication',
            entityId: $medication->id,
            patientId: $this->patient->id,
        );

        Flux::toast(text: "Discontinued {$medication->name}.");
        $this->patient->load('medications');
    }

    public function render(): View
    {
        return view('livewire.patients.patient-medications');
    }
}
