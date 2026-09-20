# Module 02: Patient Management & Master Patient Index (MPI)

## 1. Executive Overview

The Patient Management module serves as the single source of truth for patient identity, demographics, and clinical history within each practice. Designed to support comprehensive pediatric, psychiatric, and general medical records, it maintains full Master Patient Index (MPI) integrity while linking clinical data with portal and billing subsystems.

Key capabilities:
- **Medical Record Number (MRN) Auto-Generation**: Deterministic, unique patient identifiers per practice.
- **Pediatric & Psychiatric History Tracking**: Dedicated fields for past psychiatric history, developmental milestones, family history, and social determinants of health (SDOH).
- **Multi-Category History Tracking**: Allergies (with severe reaction flags), active Problem Lists (ICD-10 coded), surgical history, and family medical history.
- **Biometric & Photographic Records**: Patient avatar/photo capture for clinical safety and positive patient identification.
- **Guardian & Portal Account Linking**: Binding independent patient records to family guardian user accounts for secure portal delegation.

---

## 2. User Roles & Workflows

| Role | Permitted Actions |
| :--- | :--- |
| **Attending / Doctor** | Full view of charts; modify problem lists, allergies, medical histories, and assigned care teams. |
| **Nurse / Clinical Staff** | Create new patient records, update contact details, record vital baselines, and upload patient photos. |
| **Receptionist / Front Desk** | Search patients by MRN/Name/DOB, verify insurance details, update emergency contacts. |
| **Medical Records Officer** | Audit chart access, merge duplicate records, export complete medical records. |
| **Patient / Guardian** | View demographic information and verify emergency contacts via the Patient Portal. |

---

## 3. Database Architecture & Schema

### Primary Model: [Patient](file:///c:/Users/User/Herd/bloom/app/Models/Patient.php)
- **Table**: `patients`
- **Traits**: [BelongsToPractice](file:///c:/Users/User/Herd/bloom/app/Models/Concerns/BelongsToPractice.php), `HasFactory`, `SoftDeletes`

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | `bigint unsigned` | Primary key |
| `practice_id` | `foreignId` | Multi-tenant practice reference |
| `mrn` | `string` | Unique Medical Record Number within practice |
| `first_name` / `last_name` | `string` | Patient legal name |
| `date_of_birth` | `date` | Birthdate (cast to `date`) |
| `gender_identity` / `pronouns`| `string` | Gender identity and preferred pronouns |
| `race_ethnicity` | `string` | Race and ethnic background for demographic reporting |
| `preferred_language` | `string` | Spoken language (enforces interpreter needs) |
| `address` / `phone` / `email` | `text` / `string` | Contact coordinates |
| `emergency_contact_*` | `string` | Name, phone, and relationship |
| `primary_insurance` | `string` | Primary payer and policy ID |
| `secondary_insurance` | `string` | Secondary payer details |
| `allergies` | `text` / `json` | Known medication, food, and environmental allergens |
| `problem_list` | `text` / `json` | Active and historical ICD-10 clinical diagnoses |
| `past_medical_history` | `text` | Medical conditions and past hospitalizations |
| `surgical_history` | `text` | Historical surgical procedures and dates |
| `family_history` | `text` | Genetic and familial psychiatric/medical conditions |
| `social_history` | `text` | Living conditions, schooling, substance use, and SDOH |
| `photo_path` | `string` | Storage path for patient identification photo |
| `primary_provider_id` | `foreignId` | Reference to [User](file:///c:/Users/User/Herd/bloom/app/Models/User.php) (PCP / Attending) |
| `portal_user_id` | `foreignId` | Reference to [User](file:///c:/Users/User/Herd/bloom/app/Models/User.php) with `guardian` role |

### Key Relationships
- `encounters()`: HasMany $\rightarrow$ [Encounter](file:///c:/Users/User/Herd/bloom/app/Models/Encounter.php)
- `prescriptions()`: HasMany $\rightarrow$ [Prescription](file:///c:/Users/User/Herd/bloom/app/Models/Prescription.php)
- `medications()`: HasMany $\rightarrow$ [Medication](file:///c:/Users/User/Herd/bloom/app/Models/Medication.php)
- `labOrders()` / `labResults()`: HasMany $\rightarrow$ [LabOrder](file:///c:/Users/User/Herd/bloom/app/Models/LabOrder.php) / [LabResult](file:///c:/Users/User/Herd/bloom/app/Models/LabResult.php)
- `radiologyOrders()`: HasMany $\rightarrow$ [RadiologyOrder](file:///c:/Users/User/Herd/bloom/app/Models/RadiologyOrder.php)
- `invoices()`: HasMany $\rightarrow$ [Invoice](file:///c:/Users/User/Herd/bloom/app/Models/Invoice.php)
- `appointments()`: HasMany $\rightarrow$ [Appointment](file:///c:/Users/User/Herd/bloom/app/Models/Appointment.php)
- `assessments()`: HasMany $\rightarrow$ [Assessment](file:///c:/Users/User/Herd/bloom/app/Models/Assessment.php)
- `documents()`: HasMany $\rightarrow$ [Document](file:///c:/Users/User/Herd/bloom/app/Models/Document.php)

---

## 4. Frontend & Livewire Architecture

The patient module is organized around a high-performance, multi-tabbed clinical chart view:

### 1. Patient Directory: [PatientList](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientList.php)
- Live search by Name, MRN, Phone, or DOB.
- Quick filter by assigned Primary Care Provider (PCP).
- Instant action links to schedule, start an encounter, or open the patient chart.

### 2. Patient Intake & Edit: [PatientForm](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientForm.php)
- Comprehensive multi-section form with real-time validation.
- Photo upload using Livewire `WithFileUploads` with instant image crop/preview.
- Automatic MRN generation upon initial submission.

### 3. Patient Master Chart: [PatientDetail](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientDetail.php)
Centralized cockpit integrating 12 specialized clinical sub-components:
- **Overview Tab**: Clinical summary card, active problems, allergies banner, and emergency contacts.
- **Encounters Tab**: Timeline of all historical encounters and clinical notes.
- **Medications & Prescriptions**: [PatientMedications](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientMedications.php) & [PatientRefills](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientRefills.php).
- **Diagnostics**: [PatientLabs](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientLabs.php) & [PatientRadiology](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientRadiology.php).
- **Communication & Telehealth**: [PatientMessages](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientMessages.php) & [PatientTelehealth](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientTelehealth.php).
- **Care Coordination & Engagements**: [PatientCareCoordination](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientCareCoordination.php) & [PatientEngagements](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientEngagements.php).
- **Patient Education**: [PatientEducation](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientEducation.php).

---

## 5. Security, Auditing & HIPAA Compliance

1. **Automatic Audit Trail**: Every chart load in `PatientDetail` dispatches an audit log entry via `AuditLog::create()` recording the accessing provider's ID, timestamp, patient ID, and IP address.
2. **Access Control Safeguards**: Direct URL queries for patients belonging to other practices throw an `HttpNotFoundException` due to the global `PracticeScope`.
3. **Soft Deletion**: Patients are soft-deleted to maintain historical integrity for billing and legal health record retention mandates.

---

## 6. Testing & Quality Assurance

- [PatientPhotoUploadTest](file:///c:/Users/User/Herd/bloom/tests/Feature/PatientPhotoUploadTest.php): Tests image attachment, validation, and storage disk placement.
- [CrudDeletionTest](file:///c:/Users/User/Herd/bloom/tests/Feature/CrudDeletionTest.php): Verifies soft-delete cascades and prevents unauthorized data destruction.
- [PatientPortalTest](file:///c:/Users/User/Herd/bloom/tests/Feature/PatientPortalTest.php): Tests patient-to-guardian linking and portal data visibility.
