<?php

use App\Livewire\Portal\PortalAppointments;
use App\Livewire\Scheduling\AppointmentList;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->user->assignRole('attending');
});

test('clinician can load scheduling list and see appointments', function () {
    $this->actingAs($this->user);

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
    ]);

    $appt = Appointment::create([
        'patient_id' => $patient->id,
        'provider_id' => $this->user->id,
        'practice_id' => $this->practice->id,
        'appointment_date' => today()->format('Y-m-d'),
        'start_time' => '10:00:00',
        'end_time' => '10:30:00',
        'status' => 'scheduled',
        'notes' => 'ADHD Checkup',
    ]);

    Livewire::test(AppointmentList::class)
        ->assertOk()
        ->assertSee($patient->full_name)
        ->assertSee('ADHD Checkup');
});

test('clinician can create a new appointment', function () {
    $this->actingAs($this->user);

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
    ]);

    Livewire::test(AppointmentList::class)
        ->set('patientId', $patient->id)
        ->set('providerId', $this->user->id)
        ->set('appointmentDate', today()->format('Y-m-d'))
        ->set('startTime', '11:00')
        ->set('endTime', '11:30')
        ->set('status', 'scheduled')
        ->set('notes', 'New Evaluation')
        ->call('save');

    $this->assertDatabaseHas('appointments', [
        'patient_id' => $patient->id,
        'notes' => 'New Evaluation',
    ]);
});

test('clinician can check in and complete an appointment', function () {
    $this->actingAs($this->user);

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
    ]);

    $appt = Appointment::create([
        'patient_id' => $patient->id,
        'provider_id' => $this->user->id,
        'practice_id' => $this->practice->id,
        'appointment_date' => today()->format('Y-m-d'),
        'start_time' => '10:00:00',
        'end_time' => '10:30:00',
        'status' => 'scheduled',
    ]);

    Livewire::test(AppointmentList::class)
        ->call('updateStatus', $appt->id, 'checked_in');

    $this->assertEquals('checked_in', $appt->fresh()->status);

    Livewire::test(AppointmentList::class)
        ->call('updateStatus', $appt->id, 'completed');

    $this->assertEquals('completed', $appt->fresh()->status);
});

test('portal user can request an appointment', function () {
    $guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);
    $guardian->assignRole('guardian');

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'portal_user_id' => $guardian->id,
    ]);

    Livewire::actingAs($guardian, 'portal')
        ->test(PortalAppointments::class)
        ->set('providerId', $this->user->id)
        ->set('appointmentDate', today()->addDay()->format('Y-m-d'))
        ->set('startTime', '14:00')
        ->set('endTime', '14:30')
        ->set('notes', 'Therapy request')
        ->call('save');

    $this->assertDatabaseHas('appointments', [
        'patient_id' => $patient->id,
        'notes' => 'Therapy request',
        'status' => 'scheduled',
    ]);
});
