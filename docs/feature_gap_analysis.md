# Feature Gap Analysis — PRD vs Codebase

Date: 2026-07-07

Summary: concise mapping of the PRD's interoperability and portal requirements to the current codebase, and prioritized gaps to address next.

## Present (implemented or scaffolded)
- Patient portal linking: `portal_user_id` on `Patient` and `portal` guard and Livewire portal components (PatientPortalDashboard, PortalForms, PortalAssessment, PortalMessages).
- HL7 handling scaffold: `Hl7IntegrationService`, `PollLabResults` job, `PatientLabs` Livewire simulation, `LabResult` model, `LabResultReceived` event and listeners (InjectLabSummaryIntoEncounter).
- Direct messaging: `DirectMessagingService` with FHIR R4 Composition generator and share method.
- E-prescribing scaffold: `SurescriptsService` and Livewire medication components calling it (simulated transmissions and formulary lookups).
- Notifications & queueing: Notifications for lab results, queued jobs, ShouldQueue implementations, and Horizon-compatible queue usage.
- Smart phrases and Livewire components: `SmartPhrase` Livewire component exists.
- Audit logging: `AuditLog` model and `LogAudit` action.

## Partial / Needs hardening
- HL7 ingestion: parser exists but needs robust validation, segment parsing edge-cases, and production adapters (Mirth/Health Gorilla).
- FHIR API surface: `DirectMessagingService` can generate Composition resources, but no public FHIR REST API endpoints (Patient/DiagnosticReport/Observation resources) with OAuth2 protection.
- E-prescribing: `SurescriptsService` is simulated; needs production credentials, EPCS flow, and DEA/EHR security flows.
- DDI / formulary: simulated formulary responses; replace with FDB or real FHIR MedicationKnowledge/coverage endpoints.
- Document storage: portal uploads use `public` disk; needs S3 signed URLs & HIPAA BAA config for production.

## Missing / Not implemented
- Integration engine adapters (Mirth Connect / Rhapsody) connectors and transform mappings.
- Complete FHIR REST API endpoints (R4) and SMART on FHIR/OAuth2 flows for third-party apps.
- Surescripts production integration, EPCS signing, and prescription status webhooks.
- Direct Messaging HISP integration (real send/receive with encryption/certificates).
- Full CCD/CDA generation for external systems beyond the current Composition renderer.
- Automated OCR & Textract pipeline (currently not integrated or configured).
- Formal consent/consent-scoped sharing workflow with expiry enforcement in all share actions (some consent scaffolding exists but needs coverage).

## Prioritized next work (recommended)
1. Harden HL7 ingestion and implement robust ORU^R01 parsing + CI tests.  
2. Add FHIR R4 API endpoints for Patient/DiagnosticReport/Observation and secure them with OAuth2 (Sanctum/Passport or custom).  
3. Implement production-grade Surescripts integration and EPCS flows (or DrFirst fallback).  
4. Configure S3/minio signed URLs and move portal uploads off the public disk.  
5. Add end-to-end tests for registration, portal linking, HL7 parsing, and direct messaging.

---

I will begin by working on item (1): hardening HL7 ingestion and adding tests, unless you prefer a different priority.