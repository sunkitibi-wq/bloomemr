<?php

use App\Livewire\InventoryManager;
use App\Livewire\PopulationHealth;
use App\Livewire\PracticeAnalytics;
use App\Models\AssetMaintenance;
use App\Models\CareGap;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\PatientCohort;
use App\Models\PatientCohortMember;
use App\Models\Practice;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\PopulationHealthService;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Ops Clinic', 'slug' => 'ops-clinic']);

    $this->admin = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->admin->assignRole('attending');
});

// ─── OPTION E: INVENTORY ───────────────────────────────────────────────────

test('inventory item can be added and appears in the list', function () {
    Livewire::actingAs($this->admin, 'web')
        ->test(InventoryManager::class)
        ->set('itemName', 'Flu Vaccine')
        ->set('itemCategory', 'vaccines')
        ->set('stockQuantity', 50)
        ->set('reorderLevel', 10)
        ->call('addItem')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('inventory_items', [
        'practice_id' => $this->practice->id,
        'name' => 'Flu Vaccine',
        'stock_quantity' => 50,
        'status' => 'active',
    ]);
});

test('inventory status is low_stock when quantity is at or below reorder level', function () {
    Livewire::actingAs($this->admin, 'web')
        ->test(InventoryManager::class)
        ->set('itemName', 'Gloves (S)')
        ->set('itemCategory', 'ppe')
        ->set('stockQuantity', 8)
        ->set('reorderLevel', 10)
        ->call('addItem');

    $this->assertDatabaseHas('inventory_items', [
        'name' => 'Gloves (S)',
        'status' => 'low_stock',
    ]);
});

test('inventory service restocks an item and updates status to active', function () {
    $item = InventoryItem::create([
        'practice_id' => $this->practice->id,
        'name' => 'Syringes',
        'category' => 'medical_supplies',
        'stock_quantity' => 0,
        'reorder_level' => 20,
        'status' => 'out_of_stock',
    ]);

    $service = app(InventoryService::class);
    $service->restockItem($item, 50);

    $this->assertDatabaseHas('inventory_items', [
        'id' => $item->id,
        'stock_quantity' => 50,
        'status' => 'active',
    ]);
});

test('asset maintenance can be added via livewire component', function () {
    Livewire::actingAs($this->admin, 'web')
        ->test(InventoryManager::class)
        ->set('activeTab', 'assets')
        ->set('assetName', 'MRI Scanner Suite 1')
        ->set('serialNumber', 'MRI-2024-001')
        ->set('nextCalibrationDue', now()->addMonths(6)->toDateString())
        ->call('addAsset')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('asset_maintenances', [
        'practice_id' => $this->practice->id,
        'asset_name' => 'MRI Scanner Suite 1',
        'status' => 'operational',
    ]);
});

// ─── OPTION F: POPULATION HEALTH ──────────────────────────────────────────

test('population health page renders for attending role', function () {
    Livewire::actingAs($this->admin, 'web')
        ->test(PopulationHealth::class)
        ->assertStatus(200);
});

test('care gap can be created and resolved via the service', function () {
    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'primary_provider_id' => $this->admin->id,
    ]);

    $gap = CareGap::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $patient->id,
        'gap_type' => 'vaccine_due',
        'description' => 'Annual flu shot due.',
        'status' => 'open',
        'due_date' => now()->addMonth(),
    ]);

    $service = app(PopulationHealthService::class);
    $service->resolveCareGap($gap);

    $this->assertDatabaseHas('care_gaps', [
        'id' => $gap->id,
        'status' => 'closed',
    ]);
});

test('resolving a care gap via livewire component updates status', function () {
    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'primary_provider_id' => $this->admin->id,
    ]);

    $gap = CareGap::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $patient->id,
        'gap_type' => 'medication_review',
        'description' => 'Semi-annual ADHD review.',
        'status' => 'open',
        'due_date' => now()->addWeeks(2),
    ]);

    Livewire::actingAs($this->admin, 'web')
        ->test(PopulationHealth::class)
        ->call('resolveCareGap', $gap->id)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('care_gaps', [
        'id' => $gap->id,
        'status' => 'closed',
    ]);
});

test('cohort member can be enrolled and counted', function () {
    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'primary_provider_id' => $this->admin->id,
    ]);

    $cohort = PatientCohort::create([
        'practice_id' => $this->practice->id,
        'name' => 'Hypertension Registry',
        'description' => 'Patients with hypertension.',
    ]);

    PatientCohortMember::create([
        'cohort_id' => $cohort->id,
        'patient_id' => $patient->id,
        'joined_at' => now(),
    ]);

    expect($cohort->members()->count())->toBe(1);
});

// ─── OPTION G: ANALYTICS ──────────────────────────────────────────────────

test('practice analytics page renders for attending role', function () {
    Livewire::actingAs($this->admin, 'web')
        ->test(PracticeAnalytics::class)
        ->assertStatus(200);
});

test('analytics encounter trend returns 6 months of data', function () {
    $component = Livewire::actingAs($this->admin, 'web')
        ->test(PracticeAnalytics::class);

    $trend = $component->instance()->encounterTrend;

    expect($trend)->toHaveCount(6);
    expect($trend->first())->toHaveKey('label');
    expect($trend->first())->toHaveKey('count');
});

test('analytics revenue summary returns expected keys', function () {
    $component = Livewire::actingAs($this->admin, 'web')
        ->test(PracticeAnalytics::class);

    $revenue = $component->instance()->revenue;

    expect($revenue)->toHaveKeys(['month_revenue', 'year_revenue', 'outstanding_balance', 'unpaid_count']);
});

test('analytics demographic breakdown contains all age groups', function () {
    $component = Livewire::actingAs($this->admin, 'web')
        ->test(PracticeAnalytics::class);

    $demo = $component->instance()->demographics;

    expect($demo['age_groups'])->toHaveKeys(['0–17', '18–34', '35–49', '50–64', '65+']);
    expect($demo)->toHaveKey('total');
});
