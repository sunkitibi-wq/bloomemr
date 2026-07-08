<?php

use App\Livewire\Patients\PatientEducation;
use App\Livewire\Portal\PortalEducation;
use App\Models\EducationArticle;
use App\Models\Patient;
use App\Models\PatientEducationAssignment;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);

    $this->provider = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->provider->assignRole('attending');

    $this->guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);
    $this->guardian->assignRole('guardian');

    $this->patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'portal_user_id' => $this->guardian->id,
    ]);

    $this->article = EducationArticle::create([
        'practice_id' => $this->practice->id,
        'title' => 'Understanding ADHD',
        'category' => 'diagnosis',
        'content' => 'ADHD stands for Attention-Deficit/Hyperactivity Disorder. It is a neurodevelopmental condition.',
        'is_published' => true,
    ]);
});

test('clinician can view the education library', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientEducation::class, ['patient' => $this->patient])
        ->assertOk()
        ->assertSee('Understanding ADHD');
});

test('clinician can assign an article to the patient portal', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientEducation::class, ['patient' => $this->patient])
        ->call('assignArticle', $this->article->id)
        ->assertHasNoErrors();

    expect(PatientEducationAssignment::count())->toBe(1);
    $assignment = PatientEducationAssignment::first();
    expect($assignment->education_article_id)->toBe($this->article->id);
    expect($assignment->patient_id)->toBe($this->patient->id);
    expect($assignment->assigned_by)->toBe($this->provider->id);
});

test('assigning the same article twice does not duplicate it', function () {
    PatientEducationAssignment::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'education_article_id' => $this->article->id,
        'assigned_by' => $this->provider->id,
    ]);

    Livewire::actingAs($this->provider, 'web')
        ->test(PatientEducation::class, ['patient' => $this->patient])
        ->call('assignArticle', $this->article->id)
        ->assertHasNoErrors();

    expect(PatientEducationAssignment::count())->toBe(1);
});

test('clinician can create a new education article', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientEducation::class, ['patient' => $this->patient])
        ->set('newTitle', 'Managing Anxiety in Teens')
        ->set('newContent', 'Anxiety disorders affect millions of children and adolescents every year in the US.')
        ->set('newCategory', 'diagnosis')
        ->call('createArticle')
        ->assertHasNoErrors();

    expect(EducationArticle::where('title', 'Managing Anxiety in Teens')->exists())->toBeTrue();
});

test('guardian can browse the education library on the portal', function () {
    Livewire::actingAs($this->guardian, 'portal')
        ->test(PortalEducation::class)
        ->assertOk()
        ->assertSee('Understanding ADHD');
});

test('guardian can acknowledge an assigned article', function () {
    $assignment = PatientEducationAssignment::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'education_article_id' => $this->article->id,
        'assigned_by' => $this->provider->id,
    ]);

    Livewire::actingAs($this->guardian, 'portal')
        ->test(PortalEducation::class)
        ->call('acknowledge', $assignment->id)
        ->assertHasNoErrors();

    $assignment->refresh();
    expect($assignment->acknowledged_at)->not->toBeNull();
});

test('portal filters articles by category', function () {
    EducationArticle::create([
        'practice_id' => $this->practice->id,
        'title' => 'Sertraline Information',
        'category' => 'medication',
        'content' => 'Sertraline is an SSRI antidepressant used for depression and anxiety.',
        'is_published' => true,
    ]);

    Livewire::actingAs($this->guardian, 'portal')
        ->test(PortalEducation::class)
        ->set('selectedCategory', 'medication')
        ->assertSee('Sertraline Information')
        ->assertDontSee('Understanding ADHD');
});
