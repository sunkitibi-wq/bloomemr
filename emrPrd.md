**PRODUCT REQUIREMENTS DOCUMENT**

Customized Electronic Medical Record System

_Child & Adolescent Psychiatry & Neurodevelopmental Medicine_

| **Version** | 1.0 - Initial Release                       |
| ----------- | ------------------------------------------- |
| **Date**    | June 29, 2026                               |
| **Stack**   | Laravel v13 · Livewire v3 · Tailwind CSS v4 |
| **Status**  | Draft - Pending Stakeholder Review          |

# 1\. Executive Summary

This document defines the product requirements for a purpose-built Electronic Medical Record (EMR) system tailored to the unique workflows of child and adolescent psychiatry, autism spectrum evaluations, and neurodevelopmental medicine. The system will be developed using Laravel v13 as the backend framework, Livewire v3 for reactive, server-driven UI components, and Tailwind CSS v4 for styling - eliminating the need for a separate SPA frontend while maintaining a rich, dynamic experience.

The EMR draws design inspiration from OpenEMR's Direct messaging architecture (encrypted, standards-based health information exchange) while extending far beyond it with specialty-specific clinical tools, AI-assisted charting, integrated e-prescribing, and a patient/family portal.

# 2\. Goals & Success Metrics

## 2.1 Primary Goals

- Deliver a HIPAA-compliant, NYS-regulation-ready clinical platform for child and adolescent psychiatry practices
- Reduce documentation time per encounter by at least 40% through smart templates, Smart Phrases, and auto-population
- Enable fully integrated, paperless patient intake, assessments, prescribing, and care coordination
- Support population health analytics and outcome-based quality improvement

## 2.2 Key Success Metrics

| **Metric**                     | Target                                 |
| ------------------------------ | -------------------------------------- |
| **Note completion time**       | < 8 min/encounter                      |
| **Patient portal adoption**    | \> 70% of active patients              |
| **E-prescribing accuracy**     | 0 rejected scripts due to system error |
| **Assessment score trending**  | Available for 100% of scored tools     |
| **System uptime**              | 99.9% (< 8.7 hrs downtime/year)        |
| **HIPAA audit trail coverage** | 100% of PHI access events logged       |

# 3\. Stakeholders & User Roles

| **Role**                     | Responsibilities & Access Level                                         |
| ---------------------------- | ----------------------------------------------------------------------- |
| **Attending Physician / NP** | Full clinical access: notes, prescribing, assessments, labs, care plans |
| **Resident / Fellow**        | Supervised clinical access; notes require co-sign                       |
| **Clinical Staff / MA**      | Intake, vitals, scheduling, message triage - no prescribing             |
| **Billing / Admin**          | Billing codes, insurance, reports - no clinical notes                   |
| **Patient / Family**         | Portal access: forms, secure messaging, education, appointments         |
| **Super Admin**              | System config, user management, audit logs, feature flags               |

# 4\. Technology Stack

## 4.1 Core Framework Choices

| **Layer**             | Technology & Rationale                                                                      |
| --------------------- | ------------------------------------------------------------------------------------------- |
| **Backend Framework** | Laravel v13 - Eloquent ORM, Policy-based auth, job queues, FHIR-ready API layer             |
| **Reactive UI**       | Livewire v3 - Server-rendered components, real-time validation, no Vue/React SPA complexity |
| **Styling**           | Tailwind CSS v4 - Utility-first, consistent design system, dark mode ready                  |
| **Database**          | MySQL 8+ (primary) + Redis 7 (sessions, queues, caching)                                    |
| **File Storage**      | Laravel Filesystem → AWS S3 (HIPAA BAA eligible) or self-hosted MinIO                       |
| **Auth & 2FA**        | Laravel Sanctum + TOTP (Google Authenticator / Authy)                                       |
| **Queue System**      | Laravel Horizon + Redis for lab polling, notification dispatch, PDF generation              |
| **Search**            | Laravel Scout + MeiliSearch (instant patient/chart search)                                  |
| **PDF Generation**    | Laravel DomPDF / Browsershot for clinical note printouts                                    |
| **E-Prescribing**     | Surescripts API (primary) or DrFirst Rcopia for NYS compliance                              |
| **Lab Integration**   | HL7 v2.x / FHIR R4 via Health Gorilla or Rhapsody integration engine                        |
| **Direct Messaging**  | EMR Direct phiMail (OpenEMR-compatible HISP) for secure provider-to-provider exchange       |
| **Telehealth**        | Daily.co or Whereby embedded SDK (NYS telehealth regulations compliant)                     |
| **Hosting**           | AWS (us-east-1) - HIPAA-eligible services with signed BAA                                   |

## 4.2 Laravel-Specific Architecture Patterns

- Domain-Driven Design (DDD) with bounded contexts: Clinical, Billing, Portal, Admin
- Repository Pattern for all database interactions - testable and swappable
- Laravel Policies for every model - enforcing RBAC at the code level
- Form Requests for all user input - server-side validation with Livewire form objects
- Event-driven architecture - clinical events (NoteCreated, PrescriptionSent) dispatch listeners for audit logging, notifications, and analytics
- Laravel Telescope (dev) + Sentry (prod) for error tracking and performance monitoring

# 5\. Feature Specifications

## 5.1 Patient Demographics & Profile

Each patient has a secure, longitudinal profile managed through a Livewire component with real-time auto-save. All fields are encrypted at rest using Laravel's built-in encryption helpers on sensitive columns.

**Required Fields & Components**

| • Photo upload (stored in S3, served via signed URLs with expiry)                              |
| ---------------------------------------------------------------------------------------------- |
| • Demographics: DOB, gender identity, pronouns, race/ethnicity, preferred language             |
| • Contact info: address, phone, email with opt-in/out preferences                              |
| • Emergency contacts (multiple, with relationship and authority level)                         |
| • Insurance: primary and secondary, eligibility verification via Availity API                  |
| • Consent forms: e-signature capture (stored as timestamped PDF), version-controlled templates |
| • Problem list, allergy list, and immunization records                                         |
| • School/provider directory (for care coordination)                                            |

## 5.2 Clinical Notes Engine

Notes are the core of the system. The engine is built as a modular Livewire component that supports multiple templates and persists drafts every 30 seconds to prevent data loss.

| **Note Type**        | Structure & Behavior                                                            |
| -------------------- | ------------------------------------------------------------------------------- |
| **SOAP**             | Subjective / Objective / Assessment / Plan - auto-populates HPI from prior note |
| **DAP**              | Data / Assessment / Plan - common for therapy co-documentation                  |
| **Narrative**        | Free-text with Smart Phrase expansion inline                                    |
| **Custom Templates** | Admin-configurable via drag-and-drop section builder                            |
| **Intake / Eval**    | Extended template for initial psychiatric or ASD evaluations                    |

### 5.2.1 Auto-Population (Carry-Forward)

- On new encounter open, Livewire fires a LoadPriorEncounterData event
- Pulls: active med list, problem list, last assessment scores, last 3 labs, allergies
- Provider sees a modal: 'Carry forward the following sections?' with checkboxes per section
- Selected data is injected into the note with a carried-forward timestamp watermark

### 5.2.2 Smart Phrases

- User-editable library: stored in smart_phrases table (phrase, expansion, category, owner_id)
- Triggered in notes by typing a dot-prefix (e.g., .adhd → expands to full ADHD assessment paragraph)
- Livewire Alpine.js integration intercepts keystrokes, queries /api/smart-phrases?q= via debounced Livewire action
- Admin library (global) + personal library (per provider), with privacy flag
- Optional AI-powered next-phrase suggestion: passes last 50 tokens to Claude API, returns top 3 relevant phrases

## 5.3 Assessment Tools

Assessments are first-class objects in the system - not PDFs or scanned forms. Each instrument is configured in a JSON schema and rendered dynamically by a Livewire ScoredAssessment component.

| **Instrument**            | Population & Scoring                                               |
| ------------------------- | ------------------------------------------------------------------ |
| **PHQ-9**                 | Depression screening (adolescents); auto-scored, severity banding  |
| **GAD-7**                 | Generalized anxiety; scored with clinical cut-off flags            |
| **Vanderbilt ADHD**       | Parent & teacher forms; dual rater comparison view                 |
| **ADOS-2**                | ASD diagnostic; module selection by developmental level            |
| **CARS-2**                | Childhood Autism Rating Scale; standard and high-functioning forms |
| **SCARED**                | Screen for Anxiety-Related Disorders in children                   |
| **SNAP-IV**               | ADHD/ODD symptom checklist                                         |
| **Custom Questionnaires** | Admin-built via schema editor; scored or descriptive               |

- Trending: all scored assessments chart over time via a Chart.js Livewire component in the patient header
- Portal completion: families can complete assigned assessments on their devices before the visit
- Completion tracking: date/time, rater identity, and method (in-office, portal, provider-administered) recorded

## 5.4 Medication Management & E-Prescribing

### 5.4.1 Medication Record

- Current med list synced with e-prescribing platform on every open
- Reconciliation workflow: side-by-side patient-reported vs. system list with accept/reject per med
- Drug-drug interaction (DDI) check via First Databank (FDB) API or DailyMed FHIR endpoint on every new prescription
- Allergy cross-check fires automatically before prescribing

### 5.4.2 E-Prescribing

- Surescripts EPCS (Electronic Prescribing of Controlled Substances) integration for Schedule II-V in NYS
- DrFirst Rcopia as fallback/alternative - both expose REST APIs consumed by a Laravel service class
- Two-factor authentication required for controlled substance prescriptions (DEA mandate)
- Formulary check: insurance-specific preferred/non-preferred tier display at prescribing time
- Prescription status tracking: sent → received → dispensed, surfaced in patient timeline

### 5.4.3 Prescribing Algorithms (Clinical Decision Support)

- Evidence-based algorithms rendered as interactive step-by-step flowcharts (SVG + Alpine.js)
- ADHD Algorithm: stimulant vs. non-stimulant selection, titration guidance, monitoring labs
- Depression Algorithm: SSRIs first-line, augmentation steps, safety monitoring
- ASD comorbidity guide: aggression, anxiety, ADHD overlays
- Algorithm state saved per patient encounter for continuity across visits

### 5.4.4 Medication Teaching Materials

- Library of PDF/HTML medication info sheets (Lexicomp integration or curated internal library)
- Auto-linked to current active med list - one click to assign to patient
- Teaching log: tracks which materials were given, when, and by whom
- Patient portal displays assigned materials with confirmation checkbox

## 5.5 Laboratory Management

| **Feature**              | Implementation                                                                        |
| ------------------------ | ------------------------------------------------------------------------------------- |
| **Order Entry**          | Lab order form as Livewire modal; requisition generated as PDF                        |
| **HL7 Interface**        | ORM (order) and ORU (result) messages via Rhapsody or Mirth Connect                   |
| **FHIR Integration**     | FHIR R4 DiagnosticReport resource from Health Gorilla for Quest & LabCorp             |
| **Result Display**       | Results panel in patient chart; abnormals flagged in red with delta comparison        |
| **Auto-populate Notes**  | Laravel event LabResultReceived → injects summary into open/next encounter note       |
| **Critical Value Alert** | Livewire broadcast via Laravel Echo → provider receives in-app + email + SMS alert    |
| **Trending**             | Numeric results trended over time in Chart.js panel (e.g., TSH, CBC, metabolic panel) |

## 5.6 Dashboard

The provider dashboard is a Livewire page component with polling (every 60 seconds) for live updates. It is the landing page after login and is role-specific.

**Dashboard Widgets (Provider View)**

| • Today's schedule: appointment list with status badges (checked-in, waiting, in-progress) |
| ------------------------------------------------------------------------------------------ |
| • Task inbox: co-sign queue, lab review queue, message queue                               |
| • Patient alerts: overdue labs, upcoming medication renewals, missed appointments          |
| • Unread secure messages from patients/families                                            |
| • Pending portal forms awaiting provider review                                            |
| • Quick-access recent patients (last 10 charts opened)                                     |

## 5.7 Document Management

- Upload portal accepts PDF, DOCX, images (JPEG, PNG, TIFF) - stored in S3 with metadata in documents table
- Categories: Outside Records, IEP, Behavioral Plan, School Report, Prior Auth, Other
- OCR processing (AWS Textract) on upload - makes documents full-text searchable
- Version history: each re-upload creates a new version; all versions retained
- Fax-in gateway: integration with Sfax or Updox for inbound fax-to-document conversion

## 5.8 Patient & Family Portal

The portal is a separate Laravel application route group (/portal/\*) with its own Livewire layouts. It shares the same database but enforces strict patient-scoped policies.

**Portal Features**

| • Secure messaging: HIPAA-compliant threaded messaging with providers                    |
| ---------------------------------------------------------------------------------------- |
| • Digital intake forms: assigned by staff, completed online with e-signature             |
| • Assessment completion: scored instruments assigned by provider, completed before visit |
| • Appointment requests and real-time scheduling (Calendly-like Livewire component)       |
| • Medication teaching materials: view, download, and acknowledge receipt                 |
| • Visit summaries and after-visit notes (provider-released only)                         |
| • Education library: curated articles and videos on diagnoses and medications            |
| • Secure document upload: families can submit school reports, IEPs directly              |

## 5.9 Care Coordination & Direct Messaging

Implements the Direct Project protocol (as used by OpenEMR phiMail) for provider-to-provider secure messaging. This allows sharing of clinical summaries, referral notes, and care plans with schools, therapists, and PCPs outside the practice.

- Laravel service class wraps EMR Direct phiMail REST API
- CCD/CDA document generation via a FHIR R4 Composition resource renderer
- Consent tracking: every share is tied to a signed consent record with scope and expiry
- Share log visible to provider and patient in their respective dashboards

## 5.10 Reminders & Notifications

| **Trigger**                         | Channel & Recipient                             |
| ----------------------------------- | ----------------------------------------------- |
| **Appointment upcoming (24h & 1h)** | SMS + email → patient/guardian                  |
| **Lab result received**             | In-app badge + email → ordering provider        |
| **Lab critical value**              | In-app urgent banner + SMS → provider + on-call |
| **Medication refill due (30 days)** | In-app task + portal message → patient          |
| **Assessment due**                  | Portal notification + email → patient/guardian  |
| **Unsigned notes (> 24h)**          | In-app reminder → provider                      |
| **Co-sign pending**                 | In-app task → supervisor                        |
| **Secure message received**         | Email digest (no PHI in email body) → recipient |

All notifications dispatched via Laravel Notifications (Mail, Vonage SMS, database channel). Providers control per-channel preferences in their profile settings.

# 6\. Analytics & Quality Improvement

## 6.1 Custom Reports

- Built with Laravel's query builder + a Livewire DataTable component with export to CSV/Excel
- No-show and cancellation rates by provider, time slot, and diagnosis
- Medication adherence: refill gap analysis by patient and medication class
- Outcome measures: PHQ-9 / GAD-7 trajectory by patient cohort
- Assessment completion rates: portal vs. in-office by instrument type
- Billing and RVU reports: CPT code distribution, authorization expiry tracking

## 6.2 Population Health

- Patient cohort builder: filter by diagnosis (ICD-10), age range, med class, last visit date
- High-risk flags: patients with PHQ-9 ≥ 15 and no visit in 60+ days surfaced in dashboard
- Assessment score distribution across practice population (anonymized aggregate)
- Care gap detection: patients without recent labs, overdue assessments, or unsigned care plans

# 7\. Security & Compliance

## 7.1 HIPAA Technical Safeguards

| **Safeguard**             | Implementation                                                                                      |
| ------------------------- | --------------------------------------------------------------------------------------------------- |
| **Encryption at Rest**    | AWS S3 SSE-KMS for files; AES-256 via Laravel encryption on sensitive DB columns                    |
| **Encryption in Transit** | TLS 1.3 enforced; HSTS headers; no plain HTTP                                                       |
| **Audit Trail**           | Full PHI access log: user, IP, timestamp, patient_id, action - stored in immutable audit_logs table |
| **Auto-logout**           | Livewire session timeout after 15 minutes of inactivity with modal warning                          |
| **2FA Enforcement**       | TOTP required for all clinical staff; optional for portal users                                     |
| **Access Control**        | Laravel Gates + Policies; role-based, scoped to patient panel where applicable                      |
| **Backup**                | Automated daily snapshots (AWS RDS + S3 versioning); 30-day retention; tested quarterly             |
| **BAA**                   | Signed AWS Business Associate Agreement on file                                                     |

## 7.2 NYS-Specific Compliance

- EPCS (Electronic Prescribing for Controlled Substances): two-factor auth, Surescripts-certified workflow
- NYS Telehealth: consent documented in chart before each telehealth encounter; vendor compliant with Article 29-G
- SHIELD Act: data breach notification procedures documented in incident response plan
- Prescriber NPI and DEA number verified via NPPES API on user setup

# 8\. Phased Development Roadmap

Development is structured in 5 phases over approximately 18 months. Each phase delivers a working, testable increment.

| **Phase** | **Duration** | **Deliverables**                                                                                  | **Tech Focus**                                                    |
| --------- | ------------ | ------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------- |
| Phase 1   | Months 1-3   | Auth, RBAC, patient demographics, basic encounter notes, document upload                          | Laravel setup, Livewire core, S3, audit logging                   |
| Phase 2   | Months 4-6   | Full note engine, Smart Phrases, assessment tools, auto-populate, dashboard                       | Livewire advanced components, Chart.js, assessment scoring engine |
| Phase 3   | Months 7-10  | E-prescribing (Surescripts), lab integration (HL7/FHIR), medication management, DDI checks        | API integrations, HL7 parser, queue-based lab polling             |
| Phase 4   | Months 11-14 | Patient portal, secure messaging, Direct messaging (phiMail), care coordination, telehealth embed | Separate portal route group, Laravel Echo, WebSockets             |
| Phase 5   | Months 15-18 | Analytics, population health, AI smart phrase suggestions, prescribing algorithms, QI reports     | Laravel Scout, reporting engine, optional Claude API integration  |

Each phase concludes with a UAT sprint and security review before production deployment.

# 9\. Core Data Models (Simplified)

The following outlines the primary database entities and their key relationships.

| **Model**         | Key Fields & Relationships                                                                                     |
| ----------------- | -------------------------------------------------------------------------------------------------------------- |
| **Patient**       | id, mrn, demographics, photo_path, portal_user_id → hasMany Encounters, Meds, Labs, Assessments                |
| **Encounter**     | id, patient_id, provider_id, type, status, note_body (encrypted), signed_at → hasMany LabOrders, Prescriptions |
| **ClinicalNote**  | id, encounter_id, template_type, sections (JSON), draft (JSON), version, signed_by                             |
| **SmartPhrase**   | id, trigger, expansion, category, owner_id (nullable = global), is_ai_suggested                                |
| **Assessment**    | id, patient_id, encounter_id, instrument, rater_type, responses (JSON), score, severity_band                   |
| **Medication**    | id, patient_id, name, dose, frequency, prescriber_id, status, ndc_code                                         |
| **Prescription**  | id, medication_id, encounter_id, epcs_id, status, sent_at, dispensed_at                                        |
| **LabOrder**      | id, encounter_id, panel, status, requisition_pdf_path                                                          |
| **LabResult**     | id, lab_order_id, patient_id, result_data (JSON), is_critical, reviewed_at                                     |
| **Document**      | id, patient_id, category, file_path, ocr_text, version, uploaded_by                                            |
| **AuditLog**      | id, user_id, patient_id, action, ip_address, user_agent, created_at (immutable)                                |
| **DirectMessage** | id, sender_address, recipient_address, subject, attachment_ccd_path, status                                    |

# 10\. Testing Strategy

| **Test Type**      | Tools & Scope                                                              |
| ------------------ | -------------------------------------------------------------------------- |
| **Unit Tests**     | PHPUnit - service classes, scoring algorithms, prescription logic          |
| **Feature Tests**  | Laravel HTTP tests - all form submissions, API endpoints, auth flows       |
| **Livewire Tests** | Livewire testing helpers - component state, events, form validation        |
| **Browser Tests**  | Laravel Dusk - end-to-end patient intake, note creation, prescribing flows |
| **Security Tests** | OWASP ZAP scan, manual penetration test before each major release          |
| **HIPAA Audit**    | Third-party audit annually; internal audit log review monthly              |
| **Performance**    | Laravel Telescope + k6 load testing - target < 300ms p95 page load         |

# 11\. Open Questions & Decisions Required

- E-prescribing vendor: confirm Surescripts vs. DrFirst preference - both NYS-compliant but differ in contract and cost structure
- Lab integration: direct HL7 interface vs. Health Gorilla aggregator - Health Gorilla is faster to implement but adds per-transaction cost
- AI Smart Phrase: opt-in feature - confirm willingness to include PHI-free context snippets in API calls to Claude or another LLM
- Telehealth platform: Daily.co (simpler embed) vs. Whereby (white-label) - patient UX differs significantly
- Hosting model: AWS managed RDS vs. self-managed PostgreSQL - RDS recommended for HIPAA simplicity
- Portal identity verification: basic email/password vs. ID.me integration for higher assurance

_Confidential - For Internal Use Only | Custom EMR PRD v1.0_