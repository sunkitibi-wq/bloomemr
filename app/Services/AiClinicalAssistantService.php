<?php

namespace App\Services;

use App\Models\SmartPhrase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiClinicalAssistantService
{
    /**
     * Parse raw dialogue transcripts into structured note sections using Anthropic Claude or Mock.
     */
    public function generateDraftFromTranscript(string $templateType, string $transcript): array
    {
        if (config('services.anthropic.mock', true)) {
            return $this->mockGenerateDraftFromTranscript($templateType, $transcript);
        }

        if (empty($transcript)) {
            return [];
        }

        $systemPrompt = "You are an expert psychiatrist assistant. Convert the following patient-provider dialogue transcript into a formal {$templateType} clinical note. ".
                        'Return ONLY valid JSON. Do not include markdown code blocks (like ```json), just the raw JSON object.';

        if ($templateType === 'SOAP') {
            $systemPrompt .= ' The JSON MUST have exactly these keys: subjective, objective, assessment, plan.';
        } elseif ($templateType === 'DAP') {
            $systemPrompt .= ' The JSON MUST have exactly these keys: data, assessment, plan.';
        } elseif ($templateType === 'Intake') {
            $systemPrompt .= ' The JSON MUST have exactly these keys: reason_for_visit, hpi, past_psychiatric_history, medical_history, family_history, social_history, mental_status_exam, diagnostic_impression, plan.';
        } else {
            $systemPrompt .= ' The JSON MUST have exactly one key: body.';
        }

        $response = Http::withToken(config('services.anthropic.secret'))
            ->withHeaders([
                'anthropic-version' => '2023-06-01',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 2000,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $transcript],
                ],
            ]);

        if ($response->successful()) {
            $text = $response->json('content.0.text');
            // Clean markdown blocks if Claude includes them despite the prompt
            $text = preg_replace('/```json\s*/', '', $text);
            $text = preg_replace('/```\s*/', '', $text);

            $decoded = json_decode(trim($text), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Fallback or error handling
        return ['body' => 'Failed to generate note from transcript using AI. Error: '.$response->body()];
    }

    /**
     * Suggest relevant smart phrases based on existing note contents using Anthropic Claude or Mock.
     */
    public function suggestSmartPhrases(string $text): Collection
    {
        if (config('services.anthropic.mock', true)) {
            return $this->mockSuggestSmartPhrases($text);
        }

        if (empty($text)) {
            return collect();
        }

        $textLower = strtolower($text);

        // Fetch custom/global smart phrases matching category or keywords
        $matches = SmartPhrase::where(function ($q) use ($textLower) {
            $q->where('category', 'like', "%{$textLower}%")
                ->orWhere('trigger', 'like', "%{$textLower}%");
        })->get();

        // Generate dynamic AI phrases if no direct database match is found
        if ($matches->isEmpty()) {
            $systemPrompt = "You are an expert psychiatrist assistant. Based on the following partial clinical note text, suggest 1 to 3 useful auto-completion 'Smart Phrases' that the doctor might want to insert next. ".
                "Return ONLY valid JSON as an array of objects. Each object MUST have exactly these keys: trigger (a short 1-2 word identifier starting with 'ai_'), expansion (the full suggested sentence or paragraph), category (a short category name). Do not include markdown blocks.";

            $response = Http::withToken(config('services.anthropic.secret'))
                ->withHeaders([
                    'anthropic-version' => '2023-06-01',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => 'claude-3-haiku-20240307',
                    'max_tokens' => 500,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $text],
                    ],
                ]);

            $suggestions = collect();

            if ($response->successful()) {
                $jsonText = $response->json('content.0.text');
                $jsonText = preg_replace('/```json\s*/', '', $jsonText);
                $jsonText = preg_replace('/```\s*/', '', $jsonText);

                $decoded = json_decode(trim($jsonText), true);

                if (is_array($decoded)) {
                    foreach ($decoded as $item) {
                        $suggestions->push(new SmartPhrase([
                            'trigger' => $item['trigger'] ?? 'ai_suggestion',
                            'expansion' => $item['expansion'] ?? '',
                            'category' => $item['category'] ?? 'AI Suggestion',
                            'is_ai_suggested' => true,
                        ]));
                    }
                }
            }

            // Fallback if AI fails or returns empty
            if ($suggestions->isEmpty()) {
                $suggestions->push(new SmartPhrase([
                    'trigger' => 'ai_normal',
                    'expansion' => 'Patient denies any suicidal ideation, homicidal ideation, or auditory/visual hallucinations.',
                    'category' => 'General Safety',
                    'is_ai_suggested' => true,
                ]));
            }

            return $suggestions;
        }

        return $matches;
    }

    /**
     * Parse raw dialogue transcripts into structured note sections using hardcoded regex (Mock).
     */
    private function mockGenerateDraftFromTranscript(string $templateType, string $transcript): array
    {
        if (empty($transcript)) {
            return [];
        }

        $lines = explode("\n", $transcript);

        if ($templateType === 'SOAP') {
            $sections = [
                'subjective' => [],
                'objective' => [],
                'assessment' => [],
                'plan' => [],
            ];

            foreach ($lines as $line) {
                $lineLower = strtolower($line);
                if (Str::contains($lineLower, ['report', 'say', 'feel', 'symptom', 'pain', 'sleep', 'anxious', 'depress', 'describe'])) {
                    $sections['subjective'][] = trim($line);
                } elseif (Str::contains($lineLower, ['bp', 'pulse', 'temp', 'exam', 'vital', 'observ', 'look', 'agitat', 'alert', 'weight', 'heart'])) {
                    $sections['objective'][] = trim($line);
                } elseif (Str::contains($lineLower, ['diagnos', 'impress', 'differential', 'r/o', 'rule out', 'severity', 'dsm'])) {
                    $sections['assessment'][] = trim($line);
                } elseif (Str::contains($lineLower, ['prescrib', 'dose', 'mg', 'titrat', 'follow-up', 'refer', 'start', 'stop', 'plan', 'therapy'])) {
                    $sections['plan'][] = trim($line);
                } else {
                    // Fallback to subjective
                    $sections['subjective'][] = trim($line);
                }
            }

            return [
                'subjective' => implode(' ', $sections['subjective']) ?: 'Patient reports normal baseline symptoms.',
                'objective' => implode(' ', $sections['objective']) ?: 'Vitals stable. Psychomotor activity normal.',
                'assessment' => implode(' ', $sections['assessment']) ?: 'Clinical status stable.',
                'plan' => implode(' ', $sections['plan']) ?: 'Continue current treatment plan. Follow up as scheduled.',
            ];
        }

        if ($templateType === 'DAP') {
            $sections = [
                'data' => [],
                'assessment' => [],
                'plan' => [],
            ];

            foreach ($lines as $line) {
                $lineLower = strtolower($line);
                if (Str::contains($lineLower, ['diagnos', 'impress', 'differential', 'severity'])) {
                    $sections['assessment'][] = trim($line);
                } elseif (Str::contains($lineLower, ['prescrib', 'dose', 'mg', 'titrat', 'follow-up', 'refer', 'start', 'stop'])) {
                    $sections['plan'][] = trim($line);
                } else {
                    $sections['data'][] = trim($line);
                }
            }

            return [
                'data' => implode(' ', $sections['data']) ?: 'Session conducted. Addressed patient concerns.',
                'assessment' => implode(' ', $sections['assessment']) ?: 'Progressing towards therapeutic goals.',
                'plan' => implode(' ', $sections['plan']) ?: 'Follow up as planned.',
            ];
        }

        if ($templateType === 'Intake') {
            $sections = [
                'reason_for_visit' => [],
                'hpi' => [],
                'past_psychiatric_history' => [],
                'medical_history' => [],
                'family_history' => [],
                'social_history' => [],
                'mental_status_exam' => [],
                'diagnostic_impression' => [],
                'plan' => [],
            ];

            foreach ($lines as $line) {
                $lineLower = strtolower($line);
                if (Str::contains($lineLower, ['reason', 'come in', 'presenting'])) {
                    $sections['reason_for_visit'][] = trim($line);
                } elseif (Str::contains($lineLower, ['onset', 'duration', 'course', 'hpi', 'history of present illness'])) {
                    $sections['hpi'][] = trim($line);
                } elseif (Str::contains($lineLower, ['past psych', 'previous psychiatrist', 'prior hospital'])) {
                    $sections['past_psychiatric_history'][] = trim($line);
                } elseif (Str::contains($lineLower, ['medical', 'surgery', 'physical illness'])) {
                    $sections['medical_history'][] = trim($line);
                } elseif (Str::contains($lineLower, ['family', 'mother', 'father', 'hereditary'])) {
                    $sections['family_history'][] = trim($line);
                } elseif (Str::contains($lineLower, ['social', 'job', 'substance', 'alcohol', 'smoke'])) {
                    $sections['social_history'][] = trim($line);
                } elseif (Str::contains($lineLower, ['exam', 'mse', 'affect', 'speech', 'thought', 'cognition'])) {
                    $sections['mental_status_exam'][] = trim($line);
                } elseif (Str::contains($lineLower, ['diagnos', 'dsm', 'icd', 'rule out'])) {
                    $sections['diagnostic_impression'][] = trim($line);
                } else {
                    $sections['plan'][] = trim($line);
                }
            }

            return [
                'reason_for_visit' => implode(' ', $sections['reason_for_visit']) ?: 'Intake assessment requested by patient.',
                'hpi' => implode(' ', $sections['hpi']) ?: 'Patient describes gradual onset of clinical symptoms.',
                'past_psychiatric_history' => implode(' ', $sections['past_psychiatric_history']) ?: 'No past psychiatric hospitalizations reported.',
                'medical_history' => implode(' ', $sections['medical_history']) ?: 'Non-contributory medical history.',
                'family_history' => implode(' ', $sections['family_history']) ?: 'Denies family history of psychiatric illness.',
                'social_history' => implode(' ', $sections['social_history']) ?: 'Lives independently. No current substance use.',
                'mental_status_exam' => implode(' ', $sections['mental_status_exam']) ?: 'Alert and oriented. Euthymic mood, coherent speech.',
                'diagnostic_impression' => implode(' ', $sections['diagnostic_impression']) ?: 'Assess for mood disorder vs anxiety.',
                'plan' => implode(' ', $sections['plan']) ?: 'Establish outpatient psychiatric treatment schedule.',
            ];
        }

        return ['body' => $transcript];
    }

    /**
     * Suggest relevant smart phrases based on existing note contents (Mock).
     */
    private function mockSuggestSmartPhrases(string $text): Collection
    {
        $textLower = strtolower($text);

        // Fetch custom/global smart phrases matching category or keywords
        $matches = SmartPhrase::where(function ($q) use ($textLower) {
            $q->where('category', 'like', "%{$textLower}%")
                ->orWhere('trigger', 'like', "%{$textLower}%");
        })->get();

        // Generate dynamic AI phrases if no direct database match is found
        if ($matches->isEmpty()) {
            $suggestions = collect();

            if (Str::contains($textLower, ['anxious', 'worry', 'panic'])) {
                $suggestions->push(new SmartPhrase([
                    'trigger' => 'ai_anxiety',
                    'expansion' => 'Patient reports heightened generalized anxiety, somatic worry, and occasional panic attacks.',
                    'category' => 'Anxiety',
                    'is_ai_suggested' => true,
                ]));
            }

            if (Str::contains($textLower, ['depress', 'sad', 'hopeless'])) {
                $suggestions->push(new SmartPhrase([
                    'trigger' => 'ai_depression',
                    'expansion' => 'Patient reports pervasive low mood, anhedonia, and feelings of helplessness over the past month.',
                    'category' => 'Depression',
                    'is_ai_suggested' => true,
                ]));
            }

            if (Str::contains($textLower, ['sleep', 'insomnia', 'nightmare'])) {
                $suggestions->push(new SmartPhrase([
                    'trigger' => 'ai_sleep',
                    'expansion' => 'Reports severe sleep onset insomnia, sleeping average of 4 hours per night.',
                    'category' => 'Sleep Hygiene',
                    'is_ai_suggested' => true,
                ]));
            }

            if (Str::contains($textLower, ['med', 'compliance', 'pill'])) {
                $suggestions->push(new SmartPhrase([
                    'trigger' => 'ai_compliance',
                    'expansion' => 'Patient reports adhering strictly to medication schedule with no disruptive side effects.',
                    'category' => 'Medication Log',
                    'is_ai_suggested' => true,
                ]));
            }

            if ($suggestions->isEmpty()) {
                // Generic clinically sound phrase fallback
                $suggestions->push(new SmartPhrase([
                    'trigger' => 'ai_normal',
                    'expansion' => 'Patient denies any suicidal ideation, homicidal ideation, or auditory/visual hallucinations.',
                    'category' => 'General Safety',
                    'is_ai_suggested' => true,
                ]));
            }

            return $suggestions;
        }

        return $matches;
    }

    /**
     * Draft a secure message for a patient based on a short prompt.
     */
    public function draftPatientMessage(string $prompt): string
    {
        if (config('services.anthropic.mock', true)) {
            return "Hello Dr. Smith,\n\nI am writing to request: ".$prompt."\n\nPlease let me know if you need any additional information.\n\nThank you.";
        }

        if (empty($prompt)) {
            return '';
        }

        $systemPrompt = 'You are a helpful AI assistant. Draft a polite, clear, and concise secure medical message on behalf of a patient to their clinician based on the following prompt. Do not include markdown blocks, just the text. Keep it professional and brief.';

        $response = Http::withToken(config('services.anthropic.secret'))
            ->withHeaders(['anthropic-version' => '2023-06-01'])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 300,
                'system' => $systemPrompt,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

        if ($response->successful()) {
            $text = $response->json('content.0.text');

            return trim($text);
        }

        return 'Failed to draft message: '.$response->body();
    }

    /**
     * Explain lab results to a patient in plain English.
     */
    public function explainLabResult(array $labData): string
    {
        if (config('services.anthropic.mock', true)) {
            return 'Based on your mock lab results, your values appear to be monitored. Please consult your physician for a full explanation.';
        }

        if (empty($labData)) {
            return '';
        }

        $systemPrompt = 'You are an empathetic, reassuring medical assistant. Explain the following JSON lab results to the patient in plain English. Avoid complex jargon. If there are abnormal values (flagged High or Low), explain what they mean generally but ALWAYS state that they should discuss these with their doctor. Do not make definitive diagnoses. Do not use markdown blocks like ```json.';

        $response = Http::withToken(config('services.anthropic.secret'))
            ->withHeaders(['anthropic-version' => '2023-06-01'])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 500,
                'system' => $systemPrompt,
                'messages' => [['role' => 'user', 'content' => json_encode($labData)]],
            ]);

        if ($response->successful()) {
            return trim($response->json('content.0.text'));
        }

        return 'Failed to explain labs: '.$response->body();
    }

    /**
     * Triage patient symptoms and provide routing advice.
     */
    public function triageSymptoms(string $symptoms): array
    {
        if (config('services.anthropic.mock', true)) {
            return [
                'advice' => 'This is a mock triage response. If you are experiencing an emergency, please call 911.',
                'action' => 'routine',
            ];
        }

        if (empty($symptoms)) {
            return ['advice' => '', 'action' => 'none'];
        }

        $systemPrompt = "You are a medical triage assistant. You must analyze the patient's symptoms and return exactly ONE JSON object with two keys: 'advice' (a brief explanation of what the patient should do) and 'action' (must be exactly one of: 'emergency', 'urgent', 'telehealth', 'routine'). Do NOT provide medical diagnoses. If symptoms indicate chest pain, severe shortness of breath, stroke symptoms, or severe bleeding, action MUST be 'emergency'. Do not include markdown blocks like ```json.";

        $response = Http::withToken(config('services.anthropic.secret'))
            ->withHeaders(['anthropic-version' => '2023-06-01'])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 200,
                'system' => $systemPrompt,
                'messages' => [['role' => 'user', 'content' => $symptoms]],
            ]);

        if ($response->successful()) {
            $text = $response->json('content.0.text');
            $text = preg_replace('/```json\s*/', '', $text);
            $text = preg_replace('/```\s*/', '', $text);
            $decoded = json_decode(trim($text), true);
            if (is_array($decoded) && isset($decoded['advice']) && isset($decoded['action'])) {
                return $decoded;
            }

            return ['advice' => $text, 'action' => 'routine']; // fallback
        }

        return ['advice' => 'Failed to triage symptoms: '.$response->body(), 'action' => 'none'];
    }
}
