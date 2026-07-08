<?php

use App\Actions\Fortify\CreateNewUser;
use App\Livewire\SystemAdmin\PracticeManager;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Practice A — Bloom Child Psychiatry
    $this->practiceA = Practice::create([
        'name' => 'Practice Alpha',
        'slug' => 'practice-alpha',
        'is_active' => true,
    ]);

    // Practice B — Completely separate tenant
    $this->practiceB = Practice::create([
        'name' => 'Practice Beta',
        'slug' => 'practice-beta',
        'is_active' => true,
    ]);

    // Admin A — belongs to Practice A
    $this->adminA = User::factory()->create([
        'practice_id' => $this->practiceA->id,
        'role' => 'super_admin',
    ]);
    $this->adminA->assignRole('super_admin');

    // Admin B — belongs to Practice B
    $this->adminB = User::factory()->create([
        'practice_id' => $this->practiceB->id,
        'role' => 'super_admin',
    ]);
    $this->adminB->assignRole('super_admin');

    // Patient scoped to Practice A only
    $this->patientA = Patient::factory()->create([
        'practice_id' => $this->practiceA->id,
        'primary_provider_id' => $this->adminA->id,
        'first_name' => 'Alice',
        'last_name' => 'Alpha',
    ]);

    // Patient scoped to Practice B only
    $this->patientB = Patient::factory()->create([
        'practice_id' => $this->practiceB->id,
        'primary_provider_id' => $this->adminB->id,
        'first_name' => 'Bob',
        'last_name' => 'Beta',
    ]);
});

// ─── CROSS-TENANT ISOLATION ────────────────────────────────────────────────

test('Practice A admin can only see Practice A patients', function () {
    app()->instance('current_practice', $this->practiceA);

    $patients = Patient::all();

    expect($patients->pluck('practice_id')->unique()->toArray())->toBe([$this->practiceA->id]);
    expect($patients->pluck('first_name'))->toContain('Alice');
    expect($patients->pluck('first_name'))->not->toContain('Bob');
});

test('Practice B admin can only see Practice B patients', function () {
    app()->instance('current_practice', $this->practiceB);

    $patients = Patient::all();

    expect($patients->pluck('practice_id')->unique()->toArray())->toBe([$this->practiceB->id]);
    expect($patients->pluck('first_name'))->toContain('Bob');
    expect($patients->pluck('first_name'))->not->toContain('Alice');
});

test('new records are auto-assigned to the current_practice from container', function () {
    app()->instance('current_practice', $this->practiceA);

    $patient = Patient::create([
        'first_name' => 'Charlie',
        'last_name' => 'Test',
        'mrn' => 'MRN-99988',
        'date_of_birth' => '2015-01-01',
        'primary_provider_id' => $this->adminA->id,
    ]);

    expect($patient->practice_id)->toBe($this->practiceA->id);
});

// ─── PRACTICE ACTIVE STATUS ────────────────────────────────────────────────

test('practice activate and deactivate helpers work correctly', function () {
    $this->practiceA->deactivate();
    expect($this->practiceA->fresh()->is_active)->toBeFalse();

    $this->practiceA->activate();
    expect($this->practiceA->fresh()->is_active)->toBeTrue();
});

// ─── SYSTEM ADMIN ──────────────────────────────────────────────────────────

test('system admin user bypasses PracticeScope and sees all patients', function () {
    $systemAdmin = User::factory()->create([
        'practice_id' => null,
        'role' => 'super_admin',
        'is_system_admin' => true,
    ]);
    $systemAdmin->assignRole('super_admin');

    app()->instance('current_practice', null);
    Livewire::actingAs($systemAdmin, 'web');

    // With no practice bound and is_system_admin = true, scope is bypassed
    $count = Patient::count();
    expect($count)->toBeGreaterThanOrEqual(2); // both Practice A and B patients visible
});

test('system admin can view the practice manager', function () {
    $systemAdmin = User::factory()->create([
        'practice_id' => null,
        'role' => 'super_admin',
        'is_system_admin' => true,
    ]);
    $systemAdmin->assignRole('super_admin');
    app()->instance('current_practice', null);

    Livewire::actingAs($systemAdmin, 'web')
        ->test(PracticeManager::class)
        ->assertStatus(200);
});

test('system admin practice manager lists all practices', function () {
    $systemAdmin = User::factory()->create([
        'practice_id' => null,
        'role' => 'super_admin',
        'is_system_admin' => true,
    ]);
    $systemAdmin->assignRole('super_admin');
    app()->instance('current_practice', null);

    $component = Livewire::actingAs($systemAdmin, 'web')
        ->test(PracticeManager::class);

    $practices = $component->instance()->practices;

    // Must include both tenant practices
    expect($practices->pluck('slug'))->toContain('practice-alpha');
    expect($practices->pluck('slug'))->toContain('practice-beta');
});

test('system admin can toggle practice active status', function () {
    $systemAdmin = User::factory()->create([
        'practice_id' => null,
        'role' => 'super_admin',
        'is_system_admin' => true,
    ]);
    $systemAdmin->assignRole('super_admin');
    app()->instance('current_practice', null);

    Livewire::actingAs($systemAdmin, 'web')
        ->test(PracticeManager::class)
        ->call('togglePracticeStatus', $this->practiceA->id)
        ->assertHasNoErrors();

    expect($this->practiceA->fresh()->is_active)->toBeFalse();
});

// ─── REGISTRATION CREATES PRACTICE ─────────────────────────────────────────

test('CreateNewUser creates a new practice when practice_name is provided', function () {
    $initialPracticeCount = Practice::count();

    $action = new CreateNewUser;
    $user = $action->create([
        'name' => 'Dr. New Clinician',
        'email' => 'newclinic@test.com',
        'password' => 'secret12345',
        'password_confirmation' => 'secret12345',
        'role' => 'clinician',
        'practice_name' => 'New Specialty Clinic',
    ]);

    expect(Practice::count())->toBe($initialPracticeCount + 1);
    expect($user->practice->name)->toBe('New Specialty Clinic');
    expect($user->role)->toBe('super_admin');
});

test('slug is generated from practice name and is unique', function () {
    Practice::create(['name' => 'Duplicate Clinic', 'slug' => 'duplicate-clinic']);

    $action = new CreateNewUser;
    $user = $action->create([
        'name' => 'Dr. Another',
        'email' => 'another@test.com',
        'password' => 'secret12345',
        'password_confirmation' => 'secret12345',
        'role' => 'clinician',
        'practice_name' => 'Duplicate Clinic',
    ]);

    // Slug must differ since "duplicate-clinic" is already taken
    expect($user->practice->slug)->not->toBe('duplicate-clinic');
    expect($user->practice->slug)->toBe('duplicate-clinic-1');
});
