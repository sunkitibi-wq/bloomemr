<?php

use App\Livewire\Assessments\ScoredAssessment;
use App\Livewire\Auth\KycOnboarding;
use App\Livewire\Auth\UserSubscription;
use App\Livewire\Billing\BillingManager;
use App\Livewire\Billing\ClaimsCenter;
use App\Livewire\Dashboard;
use App\Livewire\Documents\DocumentUpload;
use App\Livewire\Encounters\EncounterForm;
use App\Livewire\Encounters\EncounterNote;
use App\Livewire\InventoryManager;
use App\Livewire\MessageCenter;
use App\Livewire\Patients\PatientDetail;
use App\Livewire\Patients\PatientForm;
use App\Livewire\Patients\PatientList;
use App\Livewire\Pharmacy\PharmacyPortal;
use App\Livewire\PopulationHealth;
use App\Livewire\Portal\PatientPortalDashboard;
use App\Livewire\Portal\PortalAppointments;
use App\Livewire\Portal\PortalAssessment;
use App\Livewire\Portal\PortalBilling;
use App\Livewire\Portal\PortalCareCoordination;
use App\Livewire\Portal\PortalDocumentUpload;
use App\Livewire\Portal\PortalEducation;
use App\Livewire\Portal\PortalForms;
use App\Livewire\Portal\PortalLabs;
use App\Livewire\Portal\PortalMessages;
use App\Livewire\Portal\PortalRadiology;
use App\Livewire\Portal\PortalRefills;
use App\Livewire\Portal\PortalTelehealth;
use App\Livewire\Portal\PortalTriage;
use App\Livewire\Portal\PortalVisitSummaries;
use App\Livewire\PracticeAnalytics;
use App\Livewire\Reporting\ClinicalQualityMeasures;
use App\Livewire\Scheduling\AppointmentList;
use App\Livewire\Scheduling\FlowBoard;
use App\Livewire\SystemAdmin\KycVerificationManager;
use App\Livewire\SystemAdmin\PracticeManager;
use App\Models\SmartPhrase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/features', 'features')->name('features');
Route::view('/how-it-works', 'how-it-works')->name('how-it-works');
Route::view('/demo', 'demo')->name('demo');

Route::middleware('guest')->group(function () {
    Route::view('/patient/register', 'livewire.auth.patient-register')->name('patient.register');
    Route::view('/patient/login', 'livewire.auth.patient-login')->name('patient.login');
    Route::view('/doctor/login', 'livewire.auth.doctor-login')->name('doctor.login');
    Route::view('/hospital/login', 'livewire.auth.hospital-login')->name('hospital.login');
    Route::view('/pharmacy/login', 'livewire.auth.pharmacy-login')->name('pharmacy.login');
});

Route::middleware(['auth:web', 'verified', 'kyc.subscribed'])->group(function () {
    Route::livewire('kyc', KycOnboarding::class)->name('kyc');
    Route::livewire('subscribe', UserSubscription::class)->name('subscribe');

    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('scheduling', AppointmentList::class)->name('scheduling');
    Route::livewire('scheduling/flow-board', FlowBoard::class)->name('scheduling.flow-board');
    Route::livewire('billing', BillingManager::class)->name('billing');
    Route::livewire('billing/claims', ClaimsCenter::class)->name('billing.claims');
    Route::livewire('billing/claims/{invoice}', \App\Livewire\Billing\ClaimManager::class)->name('billing.claims.show');
    Route::livewire('pharmacy-portal', PharmacyPortal::class)->name('pharmacy.portal');
    Route::livewire('inventory', InventoryManager::class)->name('inventory');
    Route::livewire('population-health', PopulationHealth::class)->name('population-health');
    Route::livewire('analytics', PracticeAnalytics::class)->name('analytics');
    Route::livewire('analytics/cqm', ClinicalQualityMeasures::class)->name('analytics.cqm');
    Route::livewire('messages', MessageCenter::class)->name('messages');
    Route::livewire('radiology', \App\Livewire\Radiology\RadiologyManager::class)->name('radiology');
    Route::livewire('clinical/ai-assistant', \App\Livewire\Clinical\AiAssistantDashboard::class)->name('clinical.ai-assistant');
    Route::livewire('crm/campaigns', \App\Livewire\Crm\CampaignManager::class)->name('crm.campaigns');

    // System Admin (is_system_admin = true users only)
    Route::livewire('system/practices', PracticeManager::class)
        ->middleware('can:system_admin')
        ->name('system.practices');
    Route::livewire('system/kyc', KycVerificationManager::class)
        ->middleware('can:system_admin')
        ->name('system.kyc');

    Route::prefix('patients')->name('patients.')->group(function () {
        Route::livewire('/', PatientList::class)->name('index');
        Route::livewire('create', PatientForm::class)->name('create');
        Route::livewire('{patient}', PatientDetail::class)->name('show');
        Route::livewire('{patient}/edit', PatientForm::class)->name('edit');
        Route::livewire('{patient}/assessment/create', ScoredAssessment::class)->name('assessment.create');
    });

    Route::prefix('patients/{patient}/encounters')->name('encounters.')->group(function () {
        Route::livewire('create', EncounterForm::class)->name('create');
        Route::livewire('{encounter}/edit', EncounterForm::class)->name('edit');
    });

    Route::prefix('encounters')->name('encounters.')->group(function () {
        Route::livewire('{encounter}/note', EncounterNote::class)->name('note');
    });

    Route::prefix('patients/{patient}/documents')->name('documents.')->group(function () {
        Route::livewire('upload', DocumentUpload::class)->name('upload');
    });

    Route::get('api/smart-phrases', function (Request $request) {
        $q = $request->query('q');
        if (empty($q)) {
            return response()->json([]);
        }

        $phrases = SmartPhrase::query()
            ->where('trigger', 'like', $q.'%')
            ->where(function ($query) {
                $query->where('is_global', true)
                    ->orWhere('owner_id', auth()->id());
            })
            ->limit(10)
            ->get(['id', 'trigger', 'expansion', 'category']);

        return response()->json($phrases);
    })->name('api.smart-phrases');
});

Route::middleware(['auth:portal,web', 'verified'])->group(function () {
    Route::livewire('portal', PatientPortalDashboard::class)->name('portal.dashboard');
    Route::livewire('portal/appointments', PortalAppointments::class)->name('portal.appointments');
    Route::livewire('portal/billing', PortalBilling::class)->name('portal.billing');
    Route::livewire('portal/messages', PortalMessages::class)->name('portal.messages');
    Route::livewire('portal/labs', PortalLabs::class)->name('portal.labs');
    Route::livewire('portal/triage', PortalTriage::class)->name('portal.triage');
    Route::livewire('portal/refills', PortalRefills::class)->name('portal.refills');
    Route::livewire('portal/telehealth', PortalTelehealth::class)->name('portal.telehealth');
    Route::livewire('portal/forms', PortalForms::class)->name('portal.forms');
    Route::livewire('portal/care-coordination', PortalCareCoordination::class)->name('portal.care-coordination');
    Route::livewire('portal/radiology', PortalRadiology::class)->name('portal.radiology');
    Route::livewire('portal/education', PortalEducation::class)->name('portal.education');
    Route::livewire('portal/visit-summaries', PortalVisitSummaries::class)->name('portal.visit-summaries');
    Route::livewire('portal/documents', PortalDocumentUpload::class)->name('portal.documents');
    Route::livewire('portal/assessments', PortalAssessment::class)->name('portal.assessments');
});

require __DIR__.'/settings.php';
