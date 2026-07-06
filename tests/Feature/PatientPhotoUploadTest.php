<?php

use App\Livewire\Patients\PatientForm;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');

    $this->practice = Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->user->assignRole('attending');
});

test('it can upload a patient profile picture on creation', function () {
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('avatar.jpg');

    Livewire::test(PatientForm::class)
        ->set('first_name', 'Bobby')
        ->set('last_name', 'Tables')
        ->set('date_of_birth', '2015-05-15')
        ->set('photo', $file)
        ->call('save')
        ->assertHasNoErrors();

    $patient = Patient::where('first_name', 'Bobby')->first();
    expect($patient)->not->toBeNull();
    expect($patient->photo_path)->not->toBeNull();

    Storage::disk('public')->assertExists($patient->photo_path);
});

test('it deletes old photo when a new photo is uploaded', function () {
    $this->actingAs($this->user);

    $oldPhotoPath = 'patient-photos/old.jpg';
    Storage::disk('public')->put($oldPhotoPath, 'content');

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'first_name' => 'Bobby',
        'last_name' => 'Tables',
        'photo_path' => $oldPhotoPath,
    ]);

    $newFile = UploadedFile::fake()->image('new.png');

    Livewire::test(PatientForm::class, ['patient' => $patient])
        ->set('photo', $newFile)
        ->call('save')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing($oldPhotoPath);

    $patient->refresh();
    Storage::disk('public')->assertExists($patient->photo_path);
});

test('it can navigate through steps and validates step-by-step', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientForm::class)
        ->assertSet('currentStep', 1)
        // Try to navigate to step 2 with empty fields - should fail
        ->call('nextStep')
        ->assertHasErrors(['first_name', 'last_name', 'date_of_birth'])
        ->assertSet('currentStep', 1)

        // Fill step 1 fields and navigate to step 2
        ->set('first_name', 'Bobby')
        ->set('last_name', 'Tables')
        ->set('date_of_birth', '2015-05-15')
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 2)

        // Navigate back to step 1
        ->call('previousStep')
        ->assertSet('currentStep', 1)

        // Navigate forward again
        ->call('nextStep')
        ->assertSet('currentStep', 2)

        // Navigate to step 3
        ->call('nextStep')
        ->assertSet('currentStep', 3)

        // Save patient
        ->call('save')
        ->assertHasNoErrors();

    $patient = Patient::where('first_name', 'Bobby')->first();
    expect($patient)->not->toBeNull();
});
