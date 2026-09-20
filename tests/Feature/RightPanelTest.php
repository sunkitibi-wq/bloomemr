<?php

use App\Livewire\Workspace\RightPanel;
use App\Models\User;
use App\Models\WorkspaceNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('it renders the right panel component', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $this->actingAs($user);

    Livewire::test(RightPanel::class)
        ->assertSee('AI Assistant')
        ->assertSee('Hello John')
        ->assertSee('Quick Notes')
        ->assertSee('Alerts');
});

test('it can save a workspace note', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(RightPanel::class)
        ->set('noteTitle', 'Test Note Title')
        ->set('noteBody', 'This is a test note body')
        ->call('saveNote')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('workspace_notes', [
        'user_id' => $user->id,
        'title' => 'Test Note Title',
        'body' => 'This is a test note body',
    ]);
});

test('it displays saved notes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    WorkspaceNote::create([
        'user_id' => $user->id,
        'title' => 'Existing Note',
        'body' => 'Existing Note Body',
    ]);

    Livewire::test(RightPanel::class)
        ->assertSee('Existing Note')
        ->assertSee('Existing Note Body');
});

test('it can interact with the AI assistant stub', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(RightPanel::class)
        ->set('chatInput', 'Help me find a patient')
        ->call('sendMessage')
        ->assertSee('Help me find a patient')
        ->assertSee('I am an AI stub and cannot process "Help me find a patient" yet');
});
