<?php

use App\Livewire\Patients\PatientDetail;
use App\Models\Document;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);

    $this->superAdmin = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'super_admin',
    ]);

    $this->attending = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);

    $this->patient = Patient::create([
        'practice_id' => $this->practice->id,
        'mrn' => 'MRN-99999',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'date_of_birth' => '2010-10-10',
        'preferred_language' => 'English',
    ]);

    $this->encounter = Encounter::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'provider_id' => $this->attending->id,
        'type' => 'soap',
        'status' => 'draft',
        'encounter_date' => now(),
    ]);

    $this->document = Document::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'uploaded_by' => $this->attending->id,
        'category' => 'other',
        'original_name' => 'test.pdf',
        'file_path' => 'documents/test.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1024,
        'version' => 1,
    ]);

    Storage::fake('public');
});

test('super admin can delete patient, encounter, and document', function () {
    $this->actingAs($this->superAdmin);

    // Verify document deletion
    Storage::disk('public')->put('documents/test.pdf', 'dummy content');

    Livewire::test(PatientDetail::class, ['patient' => $this->patient])
        ->call('deleteDocument', $this->document->id)
        ->assertHasNoErrors();

    expect(Document::find($this->document->id))->toBeNull();
    Storage::disk('public')->assertMissing('documents/test.pdf');

    // Verify encounter deletion (soft delete)
    Livewire::test(PatientDetail::class, ['patient' => $this->patient])
        ->call('deleteEncounter', $this->encounter->id)
        ->assertHasNoErrors();

    expect(Encounter::find($this->encounter->id))->toBeNull();
    expect(Encounter::withTrashed()->find($this->encounter->id))->not->toBeNull();

    // Verify patient deletion (soft delete)
    Livewire::test(PatientDetail::class, ['patient' => $this->patient])
        ->call('deletePatient')
        ->assertHasNoErrors()
        ->assertRedirect(route('patients.index'));

    expect(Patient::find($this->patient->id))->toBeNull();
    expect(Patient::withTrashed()->find($this->patient->id))->not->toBeNull();
});

test('attending user is unauthorized to delete patient, encounter, and document', function () {
    $this->actingAs($this->attending);

    // Document deletion should fail
    Livewire::test(PatientDetail::class, ['patient' => $this->patient])
        ->call('deleteDocument', $this->document->id)
        ->assertStatus(403);

    expect(Document::find($this->document->id))->not->toBeNull();

    // Encounter deletion should fail
    Livewire::test(PatientDetail::class, ['patient' => $this->patient])
        ->call('deleteEncounter', $this->encounter->id)
        ->assertStatus(403);

    expect(Encounter::find($this->encounter->id))->not->toBeNull();

    // Patient deletion should fail
    Livewire::test(PatientDetail::class, ['patient' => $this->patient])
        ->call('deletePatient')
        ->assertStatus(403);

    expect(Patient::find($this->patient->id))->not->toBeNull();
});
