# Module 05: Clinical Assessments & Decision Support (CDSS)

## 1. Executive Overview

The Clinical Assessments & Clinical Decision Support System (CDSS) module equips providers with validated, quantitative psychiatric and medical screening instruments while continuously scanning active charts for safety risks, drug contraindications, and preventive care gaps.

Key capabilities:
- **Standardized Scored Questionnaires**: Automated scoring, severity banding, and historical trending for instruments such as PHQ-9, GAD-7, Morse Fall Risk, and Vanderbilt ADHD.
- **Bi-Directional Assessment Completion**: Assessments can be administered directly by clinicians in clinic or assigned to patients/guardians for self-completion via the Patient Portal.
- **Drug-Drug & Drug-Allergy Interaction Checker**: Real-time safety validation during medication ordering.
- **Preventive Care Gaps**: Identifies missing screenings, overdue immunizations, and clinical quality measure (eCQM) non-compliance.

---

## 2. Supported Clinical Instruments

| Instrument Code | Full Name | Clinical Domain | Max Score | Severity Thresholds |
| :--- | :--- | :--- | :---: | :--- |
| **PHQ-9** | Patient Health Questionnaire-9 | Major Depressive Disorder | 27 | 0–4 (None), 5–9 (Mild), 10–14 (Moderate), 15–19 (Mod. Severe), 20–27 (Severe) |
| **GAD-7** | Generalized Anxiety Disorder-7 | Generalized Anxiety | 21 | 0–4 (Minimal), 5–9 (Mild), 10–14 (Moderate), 15–21 (Severe) |
| **Morse** | Morse Fall Risk Scale | Inpatient / Clinic Safety | 125 | 0–24 (Low Risk), 25–50 (Medium Risk), >50 (High Risk) |
| **C-SSRS** | Columbia Suicide Severity Rating Scale | Suicidality / Triage | Qualitative | Level 1 to Level 5 with immediate clinical alert triggering |
| **Vanderbilt** | Vanderbilt ADHD Rating Scale | Pediatric ADHD Screening | Composite | Inattention & Hyperactivity subscale scoring |

---

## 3. Database Architecture & Schema

### 1. [Assessment](file:///c:/Users/User/Herd/bloom/app/Models/Assessment.php)
- **Table**: `assessments`
- **Traits**: [BelongsToPractice](file:///c:/Users/User/Herd/bloom/app/Models/Concerns/BelongsToPractice.php), `HasFactory`

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | `bigint unsigned` | Primary key |
| `practice_id` | `foreignId` | Multi-tenant practice reference |
| `patient_id` | `foreignId` | Target patient |
| `provider_id` | `foreignId nullable` | Administering clinician (null if completed via portal) |
| `instrument` | `string` | e.g., `'PHQ-9'`, `'GAD-7'`, `'Morse'` |
| `responses` | `json` | Map of question keys to numeric responses (e.g., `{"q1": 2, "q2": 3}`) |
| `score` | `integer` | Calculated total score |
| `severity` | `string` | Clinical interpretation label |
| `is_completed` | `boolean` | Completion state |
| `completed_at` | `dateTime nullable` | Completion timestamp |
| `completed_by` | `string` | `'provider'` or `'patient'` |

### 2. [CareGap](file:///c:/Users/User/Herd/bloom/app/Models/CareGap.php)
- **Table**: `care_gaps`

| Column | Type | Description |
| :--- | :--- | :--- |
| `patient_id` | `foreignId` | Target patient |
| `title` | `string` | e.g., `'Annual Depression Screening Due'` |
| `description` | `text` | Clinical justification and clinical guideline |
| `status` | `string` | `'open'`, `'addressed'`, `'closed'` |
| `due_date` | `date` | Target deadline |

---

## 4. Backend Decision Support: [ClinicalDecisionSupportService](file:///c:/Users/User/Herd/bloom/app/Services/ClinicalDecisionSupportService.php)

The decision support engine evaluates orders and chart updates against clinical knowledge bases:

### 1. Drug Interaction Checking: `checkDrugInteractions()`
- Cross-references incoming medication orders against existing active prescriptions.
- Detects high-risk interactions (e.g., SSRI + MAOI serotonin syndrome risk, multiple CNS depressants).
- Returns severity levels (`critical`, `moderate`, `informational`) with action guidance.

### 2. Allergy Contraindications: `checkAllergies()`
- Scans patient documented allergies against the active ingredient and drug class of proposed prescriptions.
- Flags direct conflicts (e.g., prescribing Amoxicillin to a penicillin-allergic patient).

### 3. Care Gap Evaluation: `evaluateCareGaps()`
- Runs automated audit checks based on patient age, diagnoses, and last encounter date.
- Auto-generates [CareGap](file:///c:/Users/User/Herd/bloom/app/Models/CareGap.php) records when annual screenings or monitoring labs (e.g., Lithium levels, HbA1c) are overdue.

---

## 5. UI Components & Layouts

- [ScoredAssessment](file:///c:/Users/User/Herd/bloom/app/Livewire/Assessments/ScoredAssessment.php):
  - In-clinic assessment administration for clinicians.
  - Interactive radio-group questionnaire with live score computation in Alpine.js.
  - Historical score trajectory line graph showing patient improvement over time.
- [PortalAssessment](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalAssessment.php):
  - Clean, mobile-friendly questionnaire rendered in the Patient Portal.
  - Automatically updates patient record and notifies the care team upon completion.

---

## 6. Testing & Quality Assurance

- [ScoredAssessmentTest](file:///c:/Users/User/Herd/bloom/tests/Feature/ScoredAssessmentTest.php):
  - Verifies score calculation algorithms and severity classification logic for PHQ-9 and GAD-7.
  - Tests database persistence of JSON response sets and author attribution.
- [CareCoordinationTest](file:///c:/Users/User/Herd/bloom/tests/Feature/CareCoordinationTest.php):
  - Tests care gap generation and closure workflows.
