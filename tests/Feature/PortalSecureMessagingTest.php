<?php

use App\Livewire\Patients\PatientMessages;
use App\Livewire\Portal\PortalMessages;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\SecureMessage;
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
        'first_name' => 'Joey',
        'last_name' => 'Doe',
        'portal_user_id' => $this->guardian->id,
        'primary_provider_id' => $this->provider->id,
    ]);
});

test('guardian can send secure message to provider', function () {
    Livewire::actingAs($this->guardian, 'portal')
        ->test(PortalMessages::class)
        ->set('providerId', $this->provider->id)
        ->set('subject', 'Question about dosage')
        ->set('body', 'Hello, is it safe to take this with food?')
        ->call('sendMessage')
        ->assertHasNoErrors()
        ->assertStatus(200);

    expect(SecureMessage::count())->toBe(1);
    $msg = SecureMessage::first();
    expect($msg->subject)->toBe('Question about dosage');
    expect($msg->sender_id)->toBe($this->guardian->id);
    expect($msg->recipient_id)->toBe($this->provider->id);
});

test('clinician can view messages and send reply', function () {
    $message = SecureMessage::create([
        'practice_id' => $this->practice->id,
        'sender_id' => $this->guardian->id,
        'recipient_id' => $this->provider->id,
        'patient_id' => $this->patient->id,
        'subject' => 'Vitals Update',
        'body' => 'Weight is 50 lbs.',
    ]);

    Livewire::actingAs($this->provider, 'web')
        ->test(PatientMessages::class, ['patient' => $this->patient])
        ->call('selectMessage', $message->id)
        ->set('replyBody', 'Thank you for updating.')
        ->call('sendReply')
        ->assertHasNoErrors();

    expect(SecureMessage::count())->toBe(2);
    $reply = SecureMessage::orderBy('id', 'desc')->first();
    expect($reply->subject)->toBe('Re: Vitals Update');
    expect($reply->sender_id)->toBe($this->provider->id);
    expect($reply->recipient_id)->toBe($this->guardian->id);
});
