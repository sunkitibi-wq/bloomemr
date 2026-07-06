<?php

namespace App\Listeners;

use App\Events\LabResultReceived;
use App\Models\ClinicalNote;
use App\Models\Encounter;
use App\Models\LabOrder;
use App\Models\Patient;

class InjectLabSummaryIntoEncounter
{
    /**
     * Handle the event.
     */
    public function handle(LabResultReceived $event): void
    {
        $result = $event->labResult;
        $patient = $result->patient;
        if (! $patient instanceof Patient) {
            return;
        }

        // Find latest draft encounter
        $encounter = Encounter::where('patient_id', $patient->id)
            ->where('status', 'draft')
            ->latest()
            ->first();

        if (! $encounter instanceof Encounter) {
            return;
        }

        $note = $encounter->clinicalNotes()->latest()->first();

        if (! $note) {
            $template_type = strtoupper($encounter->type);
            $sections = [];
            if ($template_type === 'SOAP') {
                $sections = ['subjective' => '', 'objective' => '', 'assessment' => '', 'plan' => ''];
            } elseif ($template_type === 'DAP') {
                $sections = ['data' => '', 'assessment' => '', 'plan' => ''];
            } elseif ($template_type === 'INTAKE') {
                $sections = [
                    'reason_for_visit' => '',
                    'hpi' => '',
                    'past_psychiatric_history' => '',
                    'medical_history' => '',
                    'family_history' => '',
                    'social_history' => '',
                    'mental_status_exam' => '',
                    'diagnostic_impression' => '',
                    'plan' => '',
                ];
            } else {
                $sections = ['body' => ''];
            }

            $note = ClinicalNote::create([
                'encounter_id' => $encounter->id,
                'template_type' => $template_type,
                'sections' => $sections,
                'body' => '',
                'practice_id' => $encounter->practice_id ?? $patient->practice_id,
            ]);
        }

        if (! $note instanceof ClinicalNote) {
            return;
        }

        // Build lab summary text
        $labOrder = $result->labOrder;
        $panelName = $labOrder instanceof LabOrder ? $labOrder->panel : 'Lab Results';
        $dateStr = $result->created_at->format('M j, Y');
        $summary = "\n\n[Auto-populated {$panelName} - {$dateStr}]:\n";

        /** @var array<int, array{name: string, value: string, unit: string, range: string, flag: string}> $resultData */
        $resultData = $result->result_data;

        foreach ($resultData as $obs) {
            $flagText = ! empty($obs['flag']) && $obs['flag'] !== 'N' ? " ({$obs['flag']})" : '';
            $summary .= "- {$obs['name']}: {$obs['value']}{$flagText} {$obs['unit']}\n";
        }

        // Determine where to append the summary
        $sections = $note->sections ?? [];
        $targetSection = 'body';

        if (array_key_exists('objective', $sections)) {
            $targetSection = 'objective';
        } elseif (array_key_exists('data', $sections)) {
            $targetSection = 'data';
        } elseif (array_key_exists('medical_history', $sections)) {
            $targetSection = 'medical_history';
        }

        if (array_key_exists($targetSection, $sections)) {
            $sections[$targetSection] = trim(($sections[$targetSection] ?? '').$summary);
        } else {
            $firstKey = array_key_first($sections);
            if ($firstKey) {
                $sections[$firstKey] = trim(($sections[$firstKey] ?? '').$summary);
            }
        }

        $note->sections = $sections;

        // Re-generate body
        $body = '';
        if ($note->template_type === 'Narrative') {
            $body = $sections['body'] ?? '';
        } else {
            foreach ($sections as $sec => $val) {
                if (! empty($val)) {
                    $body .= '## '.strtoupper(str_replace('_', ' ', $sec))."\n".$val."\n\n";
                }
            }
        }

        $note->body = $body;
        $note->save();
    }
}
