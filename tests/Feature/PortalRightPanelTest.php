<?php

use App\Livewire\Portal\RightPanel;
use App\Models\User;
use App\Models\WorkspaceNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('it renders the portal right panel component', function () {
    $user = User::factory()->create(['name' => 'Jane Doe', 'role' => 'guardian']);
    $this->actingAs($user, 'portal');

    Livewire::test(RightPanel::class)
        ->assertSee('Health Assistant')
        ->assertSee('Hello Jane')
        ->assertSee('To-Do Items')
        ->assertSee('Quick Notes')
        ->assertSee('Quick Contacts');
});

test('it can save a workspace note as a portal user', function () {
    $user = User::factory()->create(['role' => 'guardian']);
    $this->actingAs($user, 'portal');

    Livewire::test(RightPanel::class)
        ->set('noteTitle', 'Portal Note')
        ->set('noteBody', 'This is a test note body for portal user')
        ->call('saveNote')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('workspace_notes', [
        'user_id' => $user->id,
        'title' => 'Portal Note',
        'body' => 'This is a test note body for portal user',
    ]);
});

test('it displays saved notes for portal user', function () {
    $user = User::factory()->create(['role' => 'guardian']);
    $this->actingAs($user, 'portal');

    WorkspaceNote::create([
        'user_id' => $user->id,
        'title' => 'Existing Portal Note',
        'body' => 'Existing Portal Note Body',
    ]);

    Livewire::test(RightPanel::class)
        ->assertSee('Existing Portal Note')
        ->assertSee('Existing Portal Note Body');
});

test('it can interact with the health assistant stub', function () {
    $user = User::factory()->create(['role' => 'guardian']);
    $this->actingAs($user, 'portal');

    Livewire::test(RightPanel::class)
        ->set('chatInput', 'Check my symptoms')
        ->call('sendMessage')
        ->assertSee('Check my symptoms')
        ->assertSee('I am a Health Assistant stub and cannot process "Check my symptoms" yet');
});
