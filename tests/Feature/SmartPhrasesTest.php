<?php

use App\Livewire\Settings\SmartPhrases;
use App\Models\Practice;
use App\Models\SmartPhrase;
use App\Models\User;
use Livewire\Livewire;

test('smart phrases settings page can be rendered', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);

    $response = $this->actingAs($user)->get(route('smart-phrases.edit'));

    $response->assertOk();
});

test('providers can create and manage their smart phrases', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);

    // Create a new phrase
    Livewire::actingAs($user)
        ->test(SmartPhrases::class)
        ->call('create')
        ->set('trigger', 'adhd')
        ->set('expansion', 'Patient meets diagnostic criteria for ADHD.')
        ->set('category', 'Psychiatry')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('smart_phrases', [
        'trigger' => 'adhd',
        'owner_id' => $user->id,
        'is_global' => false,
    ]);

    $phrase = SmartPhrase::where('trigger', 'adhd')->first();

    // Edit the phrase
    Livewire::actingAs($user)
        ->test(SmartPhrases::class)
        ->call('edit', $phrase)
        ->set('expansion', 'Updated ADHD description.')
        ->call('save')
        ->assertHasNoErrors();

    expect($phrase->refresh()->expansion)->toBe('Updated ADHD description.');

    // Delete the phrase
    Livewire::actingAs($user)
        ->test(SmartPhrases::class)
        ->call('delete', $phrase);

    $this->assertDatabaseMissing('smart_phrases', [
        'id' => $phrase->id,
    ]);
});

test('non-admin providers cannot edit or delete global smart phrases', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);

    // Create global smart phrase
    $globalPhrase = SmartPhrase::create([
        'trigger' => 'global',
        'expansion' => 'Global description text.',
        'is_global' => true,
        'owner_id' => null,
        'practice_id' => $practice->id,
    ]);

    // Attempt delete
    Livewire::actingAs($user)
        ->test(SmartPhrases::class)
        ->call('delete', $globalPhrase);

    // Phrase should still exist
    $this->assertDatabaseHas('smart_phrases', [
        'id' => $globalPhrase->id,
    ]);
});

test('smart phrases lookup API returns matches', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);

    SmartPhrase::create([
        'trigger' => 'testphrase',
        'expansion' => 'Expanded test text.',
        'owner_id' => $user->id,
        'practice_id' => $practice->id,
    ]);

    $response = $this->actingAs($user)->get(route('api.smart-phrases', ['q' => 'test']));

    $response->assertOk()
        ->assertJsonFragment([
            'trigger' => 'testphrase',
            'expansion' => 'Expanded test text.',
        ]);
});
