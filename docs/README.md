# Bloom EMR: System Architecture & Module Documentation

Welcome to the comprehensive technical and operational documentation for **Bloom EMR**, an enterprise Electronic Medical Record (EMR) and Practice Management System (PMS) purpose-built for outpatient clinics, pediatric & psychiatric practices, hospitals, and community pharmacies.

---

## 1. System Technology Stack

| Layer | Technology | Version / Spec |
| :--- | :--- | :--- |
| **Backend Framework** | [Laravel Framework](file:///c:/Users/User/Herd/bloom/composer.json#L17) | v13.x (PHP 8.3+) |
| **Reactive UI** | [Livewire](file:///c:/Users/User/Herd/bloom/composer.json#L22) + [Flux UI](file:///c:/Users/User/Herd/bloom/composer.json#L21) + [Blaze](file:///c:/Users/User/Herd/bloom/composer.json#L20) | Livewire v4.1, Flux v2, Blaze v1.0 |
| **CSS & Design** | [Tailwind CSS](file:///c:/Users/User/Herd/bloom/package.json) | v4.x |
| **Authentication** | [Laravel Fortify](file:///c:/Users/User/Herd/bloom/composer.json#L16) | TOTP 2FA, WebAuthn Passkeys |
| **API & Interoperability** | [Laravel Passport](file:///c:/Users/User/Herd/bloom/composer.json#L18) | SMART on FHIR R4, OAuth2, HL7 v2.x, DICOM |
| **AI & Transcription** | Anthropic Claude 3 API / [AiClinicalAssistantService](file:///c:/Users/User/Herd/bloom/app/Services/AiClinicalAssistantService.php) | Claude 3 Haiku / Mock Regex Driver |
| **Testing Engine** | [Pest PHP](file:///c:/Users/User/Herd/bloom/composer.json#L35) | Pest v4.x (213+ automated test cases) |

---

## 2. High-Level Architectural Diagram

```text
+----------------------------------------------------------------------------------------------------+
|                                           BLOOM EMR                                                |
+----------------------------------------------------------------------------------------------------+
|                                      Multi-Tenant Foundation                                       |
|  [Practice Scope] -----> [Hospitals & Outpatient Clinics]   |   [Community & In-House Pharmacies]  |
+-------------------------------------------------------------+--------------------------------------+
|  Clinical & Operations Engine                               |  Ancillary & Diagnostics             |
|  - Patient Demographics & MPI                               |  - Diagnostic Labs (Quest HL7)       |
|  - Encounters & SOAP / DAP / Intake Charting                |  - Radiology & PACS (DICOM Web)      |
|  - SmartPhrases Auto-expansion                              |  - E-Prescribing (Surescripts/EPCS)  |
|  - Ambient AI Clinical Assistant (Claude)                   |  - In-House Dispensing & Inventory   |
|  - Scored Assessments (PHQ-9, GAD-7, Morse)                 |                                      |
|  - Clinic Flow Board & Real-Time Scheduling                 |                                      |
+-------------------------------------------------------------+--------------------------------------+
|  Patient & Family Portal (Three-Pane Workspace)                                                    |
|  - Self-Scheduling, Intake Forms & Consents, Secure Messaging, Lab & Imaging Review, Telehealth    |
+----------------------------------------------------------------------------------------------------+
|  Financial & Interoperability Layer                                                                |
|  - Revenue Cycle Management: EDI 837P Claims, EDI 835 Remittance, 270/271 Eligibility Checks      |
|  - Payment Processing: Stripe & Paystack Gateways                                                  |
|  - Interoperability: SMART on FHIR R4 API, Direct Trust Messaging, HIE Integration, Webhooks       |
+----------------------------------------------------------------------------------------------------+
```

---

## 3. Module Documentation Directory

The application is decomposed into 15 cohesive modules, each documented with its database schema, service layer, UI architecture, and automated testing suite:

| # | Module Documentation Guide | Focus Area |
| :---: | :--- | :--- |
| **01** | [01. Multi-Tenancy, Practices & RBAC](file:///c:/Users/User/Herd/bloom/docs/modules/01-multi-tenancy-and-rbac.md) | Practice scoping, facilities, departments, roles, KYC, Fortify auth |
| **02** | [02. Patient Management & Demographics](file:///c:/Users/User/Herd/bloom/docs/modules/02-patient-management.md) | Master Patient Index, MRN auto-generation, history, photo capture |
| **03** | [03. Clinical Encounters & Charting](file:///c:/Users/User/Herd/bloom/docs/modules/03-clinical-encounters-and-charting.md) | SOAP/DAP/Intake templates, SmartPhrases engine, co-signing flows |
| **04** | [04. Ambient AI Clinical Assistant](file:///c:/Users/User/Herd/bloom/docs/modules/04-ai-clinical-assistant.md) | Claude LLM ambient dialogue transcription, smart phrase suggestions |
| **05** | [05. Clinical Assessments & CDSS](file:///c:/Users/User/Herd/bloom/docs/modules/05-clinical-assessments-and-cdss.md) | PHQ-9, GAD-7, Morse scales, drug-drug and allergy interaction checks |
| **06** | [06. Scheduling & Patient Flow Board](file:///c:/Users/User/Herd/bloom/docs/modules/06-scheduling-and-flow-board.md) | Provider calendars, clinic flow board, wait time tracking, telehealth |
| **07** | [07. Diagnostic Laboratory Management](file:///c:/Users/User/Herd/bloom/docs/modules/07-diagnostic-labs.md) | CPOE lab orders, HL7 ORM/ORU messages, reference ranges, panic alerts |
| **08** | [08. Radiology, Imaging & PACS](file:///c:/Users/User/Herd/bloom/docs/modules/08-radiology-and-imaging.md) | Imaging requisitions, DICOM Web viewer, radiologist diagnostic sign-off |
| **09** | [09. E-Prescribing & Medication Management](file:///c:/Users/User/Herd/bloom/docs/modules/09-pharmacy-and-eprescribing.md) | Surescripts electronic routing, DoseSpot EPCS, formulary checks, refills |
| **10** | [10. Pharmacy Portal & Inventory](file:///c:/Users/User/Herd/bloom/docs/modules/10-pharmacy-portal-and-inventory.md) | Dedicated dispenser portal, prescription queue, lot/expiration tracking |
| **11** | [11. Patient & Guardian Portal](file:///c:/Users/User/Herd/bloom/docs/modules/11-patient-and-guardian-portal.md) | Three-pane workspace, dependent switcher, digital forms, messaging |
| **12** | [12. Telemedicine & Virtual Care](file:///c:/Users/User/Herd/bloom/docs/modules/12-telemedicine-and-virtual-care.md) | Daily.co WebRTC video rooms, side-by-side charting, waiting rooms |
| **13** | [13. Revenue Cycle Management & Billing](file:///c:/Users/User/Herd/bloom/docs/modules/13-revenue-cycle-management-billing.md) | EDI 837P claims, EDI 835 ERAs, 270/271 eligibility, Stripe & Paystack |
| **14** | [14. Interoperability, FHIR & Standards](file:///c:/Users/User/Herd/bloom/docs/modules/14-interoperability-smart-on-fhir.md) | SMART on FHIR R4 API, OAuth2 scopes, Direct Trust, HIE connectors |
| **15** | [15. Analytics, Population Health & Ops](file:///c:/Users/User/Herd/bloom/docs/modules/15-analytics-population-health-ops.md) | Clinician utilization, eCQMs (CMS2v11), cohorts, CRM outreach, tasks |

---

## 4. Developer Quick Reference

### Running the Test Suite
Bloom uses Pest PHP for automated testing:
```bash
# Run all automated tests
php artisan test --compact

# Run a specific module test suite
php artisan test --compact tests/Feature/FhirApiTest.php
php artisan test --compact tests/Feature/MultiTenancyTest.php
php artisan test --compact tests/Feature/ClinicalNoteTest.php
```

### Static Analysis & Code Formatting
```bash
# Check PHP formatting
vendor/bin/pint --test

# Fix PHP formatting
vendor/bin/pint

# Run PHPStan static analysis
./vendor/bin/phpstan analyse
```

### Development Server
```bash
# Concurrently launch local server, queue listener, and Vite compiler
composer run dev
```
