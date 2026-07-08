<?php

use App\Jobs\DispatchHl7Message;
use App\Jobs\DispatchWebhook;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use App\Models\WebhookSubscription;
use App\Services\Hl7IntegrationService;
use App\Services\WebhookDispatcherService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $this->user = User::factory()->create(['practice_id' => $this->practice->id, 'role' => 'attending']);

    $this->patient = Patient::create([
        'practice_id' => $this->practice->id,
        'mrn' => 'HL7-001',
        'first_name' => 'Test',
        'last_name' => 'Patient',
        'date_of_birth' => '2010-01-01',
    ]);
});

test('can generate mock quest hl7 message', function () {
    $hl7 = app(Hl7IntegrationService::class);
    $order = LabOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'panel' => 'Thyroid Panel',
        'status' => 'ordered',
    ]);

    $observations = [
        ['name' => 'TSH', 'value' => 3.5, 'range' => '0.4-4.0', 'flag' => 'N', 'unit' => 'mIU/L'],
        ['name' => 'T4', 'value' => 1.2, 'range' => '0.8-1.8', 'flag' => 'N', 'unit' => 'ng/dL'],
    ];

    $msg = $hl7->generateMockQuestHl7($this->patient, $order, $observations);

    expect($msg)->toContain('MSH|')
        ->and($msg)->toContain('PID|')
        ->and($msg)->toContain('OBR|')
        ->and($msg)->toContain('OBX|');
});

test('can parse incoming hl7 oru r01', function () {
    $hl7 = app(Hl7IntegrationService::class);
    $order = LabOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'panel' => 'CMP',
        'status' => 'ordered',
    ]);

    $ts = now()->format('YmdHis');
    $payload = "MSH|^~\\&|QUEST|LAB|||{$ts}||ORU^R01|MSG10001|P|2.3\n"
        ."PID|1||{$this->patient->mrn}||Patient^Test|||||||||||||||||||||||\n"
        ."OBR|1||{$order->id}||CMP||||||||||||||||||||F\n"
        ."OBX|1|NM|GLU||95|mg/dL|70-100|N|||F\n"
        .'OBX|2|NM|BUN||14|mg/dL|7-20|N|||F';

    $result = $hl7->parseOruPayload($payload, $this->practice);

    expect($result)->not->toBeNull()
        ->and($result->patient_id)->toBe($this->patient->id)
        ->and($result->lab_order_id)->toBe($order->id);
});

test('critical hl7 result is flagged', function () {
    $hl7 = app(Hl7IntegrationService::class);
    $order = LabOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'panel' => 'CMP',
        'status' => 'ordered',
    ]);

    $ts = now()->format('YmdHis');
    $payload = "MSH|^~\\&|QUEST|LAB|||{$ts}||ORU^R01|MSG10002|P|2.3\n"
        ."PID|1||{$this->patient->mrn}||Patient^Test|||||||||||||||||||||||\n"
        ."OBR|1||{$order->id}||CMP||||||||||||||||||||F\n"
        .'OBX|1|NM|GLU||350|mg/dL|70-100|HH|||F';

    $result = $hl7->parseOruPayload($payload, $this->practice);

    expect($result->is_critical)->toBeTrue();
});

test('webhook subscription model scopes active subscriptions', function () {
    $sub = WebhookSubscription::create([
        'practice_id' => $this->practice->id,
        'name' => 'Test',
        'endpoint_url' => 'https://example.com/hook',
        'events' => ['patient.created'],
        'status' => 'active',
    ]);

    expect(WebhookSubscription::active()->count())->toBe(1);

    $sub->update(['status' => 'paused']);

    expect(WebhookSubscription::active()->count())->toBe(0);
});

test('webhook subscription failure handling pauses after 10 failures', function () {
    $sub = WebhookSubscription::create([
        'practice_id' => $this->practice->id,
        'name' => 'Flaky',
        'endpoint_url' => 'https://example.com/hook',
        'events' => ['patient.created'],
        'status' => 'active',
    ]);

    foreach (range(1, 10) as $i) {
        $sub->markFailed();
    }

    $sub->refresh();

    expect($sub->status)->toBe('paused')
        ->and($sub->paused_until)->not->toBeNull();
});

test('webhook dispatcher queues jobs for matching subscriptions', function () {
    WebhookSubscription::create([
        'practice_id' => $this->practice->id,
        'name' => 'Patient Webhook',
        'endpoint_url' => 'https://ehr.example.com/hook',
        'events' => ['patient.created', 'patient.updated'],
        'status' => 'active',
    ]);

    WebhookSubscription::create([
        'practice_id' => $this->practice->id,
        'name' => 'Encounter Webhook',
        'endpoint_url' => 'https://ehr.example.com/enc',
        'events' => ['encounter.created'],
        'status' => 'active',
    ]);

    Bus::fake();

    app(WebhookDispatcherService::class)->dispatch('patient.created', ['id' => 1], $this->practice->id);

    Bus::assertDispatched(DispatchWebhook::class, 1);
});

test('dispatch hl7 message job is queueable', function () {
    $job = new DispatchHl7Message(
        practice: $this->practice,
        hl7Payload: 'MSH|...',
        endpoint: 'https://mirth:8443/api/channels/1/messages',
    );

    expect($job)->toBeInstanceOf(ShouldQueue::class);
});
