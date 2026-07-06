<?php

namespace App\Services;

use App\Models\DirectMessage;
use App\Models\Patient;
use App\Models\PatientForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DirectMessagingService
{
    /**
     * Generate a standards-compliant FHIR R4 Composition resource (CCD) for a patient.
     *
     * @param Patient $patient
     * @return array<string, mixed>
     */
    public function generateFhirComposition(Patient $patient): array
    {
        $patient->load(['medications', 'labResults', 'encounters.clinicalNotes']);

        $activeMeds = $patient->medications->where('status', 'active');
        $recentLabs = $patient->labResults->sortByDesc('created_at')->take(5);
        $recentEncounters = $patient->encounters->sortByDesc('encounter_date')->take(5);

        // Build active medications narrative/entries
        $medsText = "<ul>";
        foreach ($activeMeds as $med) {
            $medsText .= "<li><strong>{$med->name}</strong> - Dose: {$med->dose}, Freq: {$med->frequency}</li>";
        }
        $medsText .= "</ul>";
        if ($activeMeds->isEmpty()) {
            $medsText = "<p>No active medications documented.</p>";
        }

        // Build lab observations narrative/entries
        $labsText = "<ul>";
        foreach ($recentLabs as $lab) {
            $data = is_array($lab->result_data) ? $lab->result_data : [];
            foreach ($data as $obs) {
                $flag = !empty($obs['flag']) ? " [{$obs['flag']}]" : '';
                $labsText .= "<li><strong>{$obs['name']}</strong>: {$obs['value']} {$obs['unit']} (Ref: {$obs['range']}){$flag}</li>";
            }
        }
        $labsText .= "</ul>";
        if ($recentLabs->isEmpty()) {
            $labsText = "<p>No recent lab results documented.</p>";
        }

        // Build encounters/problems narrative/entries
        $encountersText = "<ul>";
        foreach ($recentEncounters as $enc) {
            $noteText = '';
            foreach ($enc->clinicalNotes as $note) {
                $noteText .= " " . substr(strip_tags($note->body), 0, 150) . "...";
            }
            $encountersText .= "<li><strong>" . $enc->encounter_date->format('Y-m-d') . "</strong> (" . ucfirst($enc->type) . "):{$noteText}</li>";
        }
        $encountersText .= "</ul>";
        if ($recentEncounters->isEmpty()) {
            $encountersText = "<p>No recent clinical encounters documented.</p>";
        }

        return [
            'resourceType' => 'Composition',
            'id' => 'bloom-ccd-' . $patient->id . '-' . rand(1000, 9999),
            'status' => 'final',
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => '34133-9',
                        'display' => 'Summary of Episode Note',
                    ],
                ],
            ],
            'subject' => [
                'reference' => 'Patient/' . $patient->id,
                'display' => $patient->full_name,
                'mrn' => $patient->mrn,
                'birthDate' => $patient->date_of_birth->format('Y-m-d'),
                'gender' => $patient->gender_identity,
            ],
            'date' => now()->toIso8601String(),
            'author' => [
                [
                    'reference' => 'Practitioner/' . Auth::id(),
                    'display' => Auth::user()->name,
                ],
            ],
            'title' => 'Continuity of Care Document (CCD)',
            'section' => [
                [
                    'title' => 'Active Medication List',
                    'code' => [
                        'coding' => [
                            [
                                'system' => 'http://loinc.org',
                                'code' => '10160-0',
                                'display' => 'History of Medication Use Narrative',
                            ],
                        ],
                    ],
                    'text' => [
                        'status' => 'generated',
                        'div' => "<div xmlns=\"http://www.w3.org/1999/xhtml\">{$medsText}</div>",
                    ],
                ],
                [
                    'title' => 'Recent Diagnostic Results',
                    'code' => [
                        'coding' => [
                            [
                                'system' => 'http://loinc.org',
                                'code' => '30954-2',
                                'display' => 'Relevant Diagnostic Tests/Laboratory Data Narrative',
                            ],
                        ],
                    ],
                    'text' => [
                        'status' => 'generated',
                        'div' => "<div xmlns=\"http://www.w3.org/1999/xhtml\">{$labsText}</div>",
                    ],
                ],
                [
                    'title' => 'Clinical Encounters History',
                    'code' => [
                        'coding' => [
                            [
                                'system' => 'http://loinc.org',
                                'code' => '46240-8',
                                'display' => 'History of Encounters Narrative',
                            ],
                        ],
                    ],
                    'text' => [
                        'status' => 'generated',
                        'div' => "<div xmlns=\"http://www.w3.org/1999/xhtml\">{$encountersText}</div>",
                    ],
                ],
            ],
        ];
    }

    /**
     * Share a patient clinical summary to an external provider via simulated Direct Messaging (phiMail).
     *
     * @param Patient $patient
     * @param string $recipientName
     * @param string $recipientAddress
     * @param string $subject
     * @param string $scope
     * @param int|null $consentFormId
     * @param string|null $expiresAt
     * @return DirectMessage
     */
    public function sendDirectMessage(
        Patient $patient,
        string $recipientName,
        string $recipientAddress,
        string $subject,
        string $scope,
        ?int $consentFormId,
        ?string $expiresAt
    ): DirectMessage {
        // Enforce ROI signed consent form check
        if (empty($consentFormId)) {
            throw new \InvalidArgumentException('A signed Release of Information (ROI) consent record is required to share clinical records.');
        }

        $consent = PatientForm::where('patient_id', $patient->id)
            ->where('status', 'completed')
            ->findOrFail($consentFormId);

        // Generate the FHIR R4 Composition payload
        $fhirPayload = $this->generateFhirComposition($patient);

        // Simulate HISP transmission (phiMail gateway callback)
        // In production this wraps an HTTP client to EMR Direct HISP servers
        $status = 'delivered'; 

        $senderAddress = strtolower(str_replace(' ', '.', Auth::user()->name)) . '@direct.bloom.test';

        $directMessage = DirectMessage::create([
            'practice_id' => $patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $patient->id,
            'patient_form_id' => $consent->id,
            'sender_id' => Auth::id(),
            'sender_address' => $senderAddress,
            'recipient_address' => $recipientAddress,
            'recipient_name' => $recipientName,
            'subject' => $subject,
            'scope' => $scope,
            'payload' => $fhirPayload,
            'status' => $status,
            'expires_at' => $expiresAt ? now()->parse($expiresAt) : null,
        ]);

        return $directMessage;
    }
}
