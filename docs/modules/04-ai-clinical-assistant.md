# Module 04: Ambient AI Clinical Assistant

## 1. Executive Overview

The Ambient AI Clinical Assistant module in Bloom EMR bridges the gap between patient dialogue and formal medical documentation. Leveraging large language models (LLMs) via the Anthropic Claude API, it transforms raw audio transcripts or clinician voice dictations into structured, specialty-tailored clinical notes (SOAP, DAP, and Psychiatric Intake).

Key capabilities:
- **Ambient Dialogue Synthesis**: Converts multi-speaker patient-provider conversational transcripts into formal clinical language.
- **Specialty Context Injection**: Injects known patient history, active problem lists, allergies, and current medications into prompt contexts for heightened clinical accuracy.
- **SmartPhrase Recommendations**: Contextually suggests relevant clinical phrases based on the active note content.
- **Patient-Friendly Explanations**: Translates complex diagnostic lab results and clinical summaries into plain-language guidance for patients on the portal.
- **Zero-Failure Fallback (Mock Engine)**: Operates seamlessly in offline or non-API development environments via a deterministic regex-based clinical parser.

---

## 2. Architecture & Processing Pipeline

```mermaid
sequenceDiagram
    autonumber
    actor Clinician as Provider
    participant UI as EncounterNote / AiDashboard
    participant Service as AiClinicalAssistantService
    participant Claude as Anthropic Claude 3 API
    participant Note as ClinicalNote (DB)

    Clinician->>UI: Pastes/Dictates Dialogue Transcript
    UI->>Service: generateDraftFromTranscript(template, transcript, patientContext)
    alt API Enabled (services.anthropic.mock = false)
        Service->>Claude: POST /v1/messages (System prompt + Transcript + Context)
        Claude-->>Service: Valid JSON Note Object
    else Mock Fallback Mode
        Service->>Service: mockGenerateDraftFromTranscript() (Regex parsing)
    end
    Service-->>UI: Return Structured Note JSON
    UI->>Clinician: Preview & Interactive Section Editing
    Clinician->>UI: Accept & Save
    UI->>Note: Persist Note Sections
```

---

## 3. Backend Implementation: [AiClinicalAssistantService](file:///c:/Users/User/Herd/bloom/app/Services/AiClinicalAssistantService.php)

### 1. Note Synthesis: `generateDraftFromTranscript()`
Accepts:
- `string $templateType`: e.g., `'SOAP'`, `'DAP'`, `'Intake'`.
- `string $transcript`: Conversational or dictation text.
- `array $patientContext`: Known diagnoses, age, allergies, and active medications.

Enforces strict JSON schema generation:
- **SOAP**: `{ subjective, objective, assessment, plan }`
- **DAP**: `{ data, assessment, plan }`
- **Intake**: `{ reason_for_visit, hpi, past_psychiatric_history, medical_history, family_history, social_history, mental_status_exam, diagnostic_impression, plan }`

```php
// app/Services/AiClinicalAssistantService.php
$response = Http::withToken(config('services.anthropic.secret'))
    ->withHeaders(['anthropic-version' => '2023-06-01'])
    ->post('https://api.anthropic.com/v1/messages', [
        'model' => 'claude-3-haiku-20240307',
        'max_tokens' => 2000,
        'system' => $systemPrompt,
        'messages' => [
            ['role' => 'user', 'content' => $transcript],
        ],
    ]);
```

### 2. Intelligent SmartPhrase Suggestions: `suggestSmartPhrases()`
Analyzes the clinician's active draft and queries the [SmartPhrase](file:///c:/Users/User/Herd/bloom/app/Models/SmartPhrase.php) library, returning phrases that match the documented clinical conditions (e.g., suggesting `@adhd_plan` when ADHD symptoms are noted).

### 3. Patient Lab Explanation & Triage
- `explainLabResults(LabResult $result)`: Formulates a reassuring, easy-to-understand explanation for portal release.
- `generateTriageAdvice(string $symptoms)`: Evaluates patient self-reported symptoms on the portal and generates safe non-emergency triage instructions with emergency escalation disclaimers.

---

## 4. UI & Livewire Integration

### 1. Dedicated AI Workspace: [AiAssistantDashboard](file:///c:/Users/User/Herd/bloom/app/Livewire/Clinical/AiAssistantDashboard.php)
- Test sandbox for clinical teams to benchmark ambient transcript parsing.
- Model selector and response latency monitoring.
- History of generated drafts with token usage metrics.

### 2. Integrated Charting Copilot: [EncounterNote](file:///c:/Users/User/Herd/bloom/app/Livewire/Encounters/EncounterNote.php)
- Clinicians can open the **AI Scribe** drawer during an active encounter.
- Live ambient audio or pasted audio transcripts are processed with one click.
- Clinicians can inspect each section (Subjective, Objective, Assessment, Plan) independently and insert them into the encounter note.

---

## 5. Security & HIPAA Considerations

1. **No Data Training**: API agreements with Anthropic guarantee that transmitted clinical data is not retained for model training.
2. **Context Minimization**: Only necessary clinical identifiers (age, gender, active conditions) are transmitted; full MRNs, names, and contact coordinates are omitted from the LLM prompt payload.
3. **Clinician-in-the-Loop Mandate**: AI-generated notes are never auto-signed; an authorized clinician must explicitly review, edit, and sign every note.

---

## 6. Testing & Quality Assurance

- [AmbientAiDocumentationTest](file:///c:/Users/User/Herd/bloom/tests/Feature/AmbientAiDocumentationTest.php):
  - Tests structured JSON extraction across SOAP, DAP, and Intake templates.
  - Tests mock engine fallback when API credentials are unconfigured.
  - Validates smart phrase suggestion accuracy.
