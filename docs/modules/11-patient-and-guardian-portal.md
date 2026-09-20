# Module 11: Patient & Guardian Portal

## 1. Executive Overview

The Patient & Guardian Portal provides patients and authorized family guardians with a modern, HIPAA-compliant digital front door to their healthcare. Featuring an interactive multi-pane workspace layout, the portal empowers patients with self-service appointment scheduling, digital intake form completion, real-time secure messaging with their care team, lab and imaging review, prescription refill requests, and virtual telehealth visits.

Key capabilities:
- **Dedicated Three-Pane Workspace**: Built with a persistent navigation sidebar, primary responsive workspace, and collapsible auxiliary right panel for appointments and active tasks.
- **Pediatric & Family Guardian Delegation**: Allows parents and legal guardians to manage multiple dependent children from a single unified login.
- **Paperless Intake & Consents**: Digital intake questionnaires and consent forms rendered via [PortalForms](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalForms.php) with instant signature capture.
- **Secure Asynchronous Messaging**: Encrypted provider-patient messaging with attachment support via [PortalMessages](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalMessages.php).
- **AI-Assisted Symptom Triage**: Interactive preliminary triage symptom checker powered by [AiClinicalAssistantService](file:///c:/Users/User/Herd/bloom/app/Services/AiClinicalAssistantService.php).

---

## 2. Portal Workspace Architecture

The portal UI utilizes a specialized three-panel layout defined in [workspace.blade.php](file:///c:/Users/User/Herd/bloom/resources/views/components/layouts/portal/workspace.blade.php):

```text
+------------------+-----------------------------------------------+------------------------+
|                  | [Top Navigation: Patient Selector, Search]   |                        |
|  Portal Sidebar  +-----------------------------------------------+  Collapsible Aux Panel |
|                  |                                               |                        |
|  - Dashboard     |                                               |  - Next Appointment    |
|  - Appointments  |                 Main Content Area             |  - Unread Messages     |
|  - Messages      |                                               |  - Pending Tasks/Forms |
|  - Labs          |           {{ $slot }} Livewire Page           |  - Care Team Contacts  |
|  - Prescriptions |                                               |                        |
|  - Billing       |                                               |  (livewire:portal.     |
|  - Telehealth    |                                               |   right-panel)         |
|  - Forms & Docs  |                                               |                        |
+------------------+-----------------------------------------------+------------------------+
```

---

## 3. Database Architecture & Schema

### 1. [PatientForm](file:///c:/Users/User/Herd/bloom/app/Models/PatientForm.php)
- **Table**: `patient_forms`
- Stores digital intake forms, medical history disclosures, and signed consent documents.
- Includes form schemas, completed answers (`json`), signature timestamps, and completion statuses (`pending`, `completed`).

### 2. [SecureMessage](file:///c:/Users/User/Herd/bloom/app/Models/SecureMessage.php)
- **Table**: `secure_messages`
- Manages encrypted provider-patient dialogue threads.
- Tracks sender (`User`), recipient (`User` or care team), subject, body, attachment links, and read receipts.

### 3. [EducationArticle](file:///c:/Users/User/Herd/bloom/app/Models/EducationArticle.php)
- **Table**: `education_articles`
- Stores curated educational guides, condition management tips (e.g., ADHD behavioral management, autism support resources), and medication guides assigned to patients by providers.

---

## 4. Livewire Portal Components

The portal features 16 specialized Livewire components located in `app/Livewire/Portal/`:

| Component | Route Name | Clinical Function |
| :--- | :--- | :--- |
| [PatientPortalDashboard](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PatientPortalDashboard.php) | `portal.dashboard` | Portal overview, recent updates, upcoming visits, health alerts |
| [PortalAppointments](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalAppointments.php) | `portal.appointments` | Self-service scheduling, appointment cancellation, history |
| [PortalBilling](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalBilling.php) | `portal.billing` | Invoices, itemized statements, Stripe/Paystack card payments |
| [PortalMessages](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalMessages.php) | `portal.messages` | Secure inbox, conversation threads, messaging care team |
| [PortalLabs](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalLabs.php) | `portal.labs` | Lab test results with AI plain-language interpretations |
| [PortalRadiology](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalRadiology.php) | `portal.radiology` | Diagnostic imaging reports and study summaries |
| [PortalRefills](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalRefills.php) | `portal.refills` | One-click prescription refill requests |
| [PortalTelehealth](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalTelehealth.php) | `portal.telehealth` | In-browser Daily.co video room consultation |
| [PortalForms](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalForms.php) | `portal.forms` | Pre-visit intake forms, consents, PHI release authorizations |
| [PortalTriage](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalTriage.php) | `portal.triage` | Symptom self-reporting and AI guidance |
| [PortalAssessment](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalAssessment.php) | `portal.assessments` | Patient self-completion of PHQ-9, GAD-7, and Vanderbilt scales |
| [PortalVisitSummaries](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalVisitSummaries.php) | `portal.visit-summaries` | After-visit summaries (AVS), diagnoses, discharge instructions |
| [PortalDocumentUpload](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalDocumentUpload.php) | `portal.documents` | Secure patient upload of external records, insurance cards, IEPs |
| [PortalEducation](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalEducation.php) | `portal.education` | Educational library of provider-assigned clinical articles |
| [PortalCareCoordination](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PortalCareCoordination.php) | `portal.care-coordination`| Multidisciplinary care plan tracking, school IEP coordination |
| [RightPanel](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/RightPanel.php) | (Embedded Layout) | Contextual auxiliary drawer showing vital alerts and reminders |

---

## 5. Guardian Account Delegation

When a user registers or logs in with the `guardian` role:
1. The portal navigation header renders a **Dependent Patient Switcher** dropdown.
2. Selecting a dependent updates the session context, automatically filtering all appointments, forms, messages, and clinical summaries to that specific child.
3. Multiple guardians (e.g., both parents) can be authorized for the same pediatric record with full audit logging of access.

---

## 6. Testing & Quality Assurance

- [PatientPortalTest](file:///c:/Users/User/Herd/bloom/tests/Feature/PatientPortalTest.php):
  - Validates authentication gates ensuring portal users only access linked patient records.
- [PortalFormsTest](file:///c:/Users/User/Herd/bloom/tests/Feature/PortalFormsTest.php):
  - Tests digital form completion, JSON answer storage, and status updates.
- [PortalSecureMessagingTest](file:///c:/Users/User/Herd/bloom/tests/Feature/PortalSecureMessagingTest.php):
  - Tests bi-directional message sending between patient and provider.
- [PortalEducationTest](file:///c:/Users/User/Herd/bloom/tests/Feature/PortalEducationTest.php):
  - Verifies assigned educational material visibility and read tracking.
