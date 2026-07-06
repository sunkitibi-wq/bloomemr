# Walkthrough: EMR Features & RBAC Integration

We resolved pre-existing test failures, implemented CRUD deletion operations, integrated the `spatie/laravel-permission` package for roles and permissions (RBAC), and completed the development of Phase 3 (E-Prescribing, HL7/FHIR lab polling, automatic note injection, and interactive CDS flowcharts).

---

## 1. Resolved Pre-existing Test Failures

### Medication Prescribing Test Crash
- **Issue**: Tests in [MedicationPrescribingTest.php](file:///c:/Users/User/Herd/bloom/tests/Feature/MedicationPrescribingTest.php) crashed with `Attempt to read property "name" on null` when rendering medications without a prescriber.
- **Fix**: Updated [patient-medications.blade.php](file:///c:/Users/User/Herd/bloom/resources/views/livewire/patients/patient-medications.blade.php) to use the null-safe operator and a fallback for unknown/external prescribers:
  ```html
  {{ $med->prescriber?->name ?? __('Unknown') }}
  ```

### Lab Management Delta Calculation Failure
- **Issue**: The test `can compute delta change against prior results` in [LabManagementTest.php](file:///c:/Users/User/Herd/bloom/tests/Feature/LabManagementTest.php) failed due to identical timestamps.
- **Fix**: Updated the test to query the latest result ordered by primary key ID:
  ```php
  $latestResult = LabResult::orderBy('id', 'desc')->first();
  ```

---

## 2. Completed CRUD (Delete Operations)

We added destructive delete capabilities for Patients, Encounters, and Documents on the Patient Detail view.

### Backend Implementation
- **File modified**: [PatientDetail.php](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientDetail.php)
- **Features added**:
  1. `deletePatient()`: Authorizes request, soft deletes patient record, and redirects.
  2. `deleteEncounter(Encounter $encounter)`: Authorizes and soft deletes encounter.
  3. `deleteDocument(Document $document)`: Authorizes, deletes physical file, and deletes database record.

### Frontend Implementation
- **File modified**: [patient-detail.blade.php](file:///c:/Users/User/Herd/bloom/resources/views/livewire/patients/patient-detail.blade.php)
  - Added guarded delete buttons with confirmations next to patients, encounters, and documents.

---

## 3. Integrated Spatie Laravel Permission (RBAC)

We replaced hardcoded role checks with dynamic roles and permissions.

### Database Seeding
- **File created**: [RoleAndPermissionSeeder.php](file:///c:/Users/User/Herd/bloom/database/seeders/RoleAndPermissionSeeder.php)
  - Defines fine-grained permissions and maps them to clinical roles.
- **File modified**: [DatabaseSeeder.php](file:///c:/Users/User/Herd/bloom/database/seeders/DatabaseSeeder.php)
  - Registered the role/permission seeder.

### Core Architecture & Event Synchronization
- **File modified**: [User.php](file:///c:/Users/User/Herd/bloom/app/Models/User.php)
  - Uses `HasRoles` trait and observer hooks to sync standard `role` column edits with Spatie roles, maintaining 100% backward-compatibility.
- **File modified**: [AppServiceProvider.php](file:///c:/Users/User/Herd/bloom/app/Providers/AppServiceProvider.php)
  - Registers `Gate::before` callback to bypass checks for `super_admin`.

### Access Control Policies & Testing Suite
- Refactored [PatientPolicy.php](file:///c:/Users/User/Herd/bloom/app/Policies/PatientPolicy.php), [EncounterPolicy.php](file:///c:/Users/User/Herd/bloom/app/Policies/EncounterPolicy.php), [DocumentPolicy.php](file:///c:/Users/User/Herd/bloom/app/Policies/DocumentPolicy.php), and [ClinicalNotePolicy.php](file:///c:/Users/User/Herd/bloom/app/Policies/ClinicalNotePolicy.php) to use Spatie checks.
- Configured [Pest.php](file:///c:/Users/User/Herd/bloom/tests/Pest.php) to run `RoleAndPermissionSeeder` in `beforeEach` for all feature tests.

---

## 4. Completed Phase 3 Features (E-Prescribing & Lab Integrations)

### E-Prescribing & Formulary Checks
- **Surescripts Service**: Created [SurescriptsService.php](file:///c:/Users/User/Herd/bloom/app/Services/SurescriptsService.php) to simulate insurance formulary checks (returning drug tier status, copays, and prior auth flags) and prescription transmissions.
- **Formulary Check UI**: Integrated formulary details in the [PatientMedications.php](file:///c:/Users/User/Herd/bloom/app/Livewire/Patients/PatientMedications.php) component. When the provider types a medication name in the prescribing form, the formulary tier, copay, and prior authorization status are dynamically loaded and displayed as visual badges.

### Lab Polling Job & Automated Note Injection
- **Background Job**: Created [PollLabResults.php](file:///c:/Users/User/Herd/bloom/app/Jobs/PollLabResults.php) to simulate polling HL7 Health Gorilla results, parsing observations, and importing results.
- **Event & Listener**: Created [LabResultReceived.php](file:///c:/Users/User/Herd/bloom/app/Events/LabResultReceived.php) and [InjectLabSummaryIntoEncounter.php](file:///c:/Users/User/Herd/bloom/app/Listeners/InjectLabSummaryIntoEncounter.php). When a lab result is received, it automatically formats and appends a markdown summary (e.g. `TSH: 6.20 (H) uIU/mL`) into the objective section of the patient's active draft encounter note.

### Interactive Clinical Decision Support (CDS)
- **Flowchart UI**: Wrapped the note editor layout in a two-column grid on large screens. The right column houses an interactive side-panel for evidence-based decision guidance (ADHD Titration and Depression SSRI Selection).
- **Titration Guide Actions**: In [EncounterNote.php](file:///c:/Users/User/Herd/bloom/app/Livewire/Encounters/EncounterNote.php), added action states and methods to let providers click through titration recommendations and insert the flowchart summary directly into the active Note's plan text with a single click.

### Medication Teaching Logs
- **Migration & Model**: Created [create_medication_teaching_logs_table.php](file:///c:/Users/User/Herd/bloom/database/migrations/2026_07_01_035307_create_medication_teaching_logs_table.php) and [MedicationTeachingLog.php](file:///c:/Users/User/Herd/bloom/app/Models/MedicationTeachingLog.php) to log when education materials/agreements are assigned.
- **Assignment UI**: Displayed a card in [patient-medications.blade.php](file:///c:/Users/User/Herd/bloom/resources/views/livewire/patients/patient-medications.blade.php) listing available Lexicomp information sheets that can be assigned to the family with one click, along with a delivery history log.

---

## 5. Roles & Permissions Management (Super Admin)

We added a dedicated administrative area for Managing Roles & Permissions under Settings.

### Backend Implementation
- **File created**: [RolesAndPermissions.php](file:///c:/Users/User/Herd/bloom/app/Livewire/Settings/RolesAndPermissions.php)
  - Aborts with 403 unless the authenticated user is a `super_admin`.
  - Supports role management (creating new roles, syncing their assigned Spatie permissions, and preventing deletion of roles that have active users assigned).
  - Supports permission management (inline creation of new Spatie permissions, and preventing deletion of permissions that are currently assigned to any role).
- **File modified**: [settings.php](file:///c:/Users/User/Herd/bloom/routes/settings.php)
  - Registered `settings/roles` Livewire route under verified auth.
- **File modified**: [Role.php](file:///c:/Users/User/Herd/bloom/app/Enums/Role.php)
  - Added `Guardian` role enum case and mapped it so that it gets resolved correctly.

### Frontend UI
- **File created**: [roles-and-permissions.blade.php](file:///c:/Users/User/Herd/bloom/resources/views/livewire/settings/roles-and-permissions.blade.php)
  - Features two tables: Roles and Permissions, complete with modal dialogs and inline forms for creation and editing.
- **File modified**: [layout.blade.php](file:///c:/Users/User/Herd/bloom/resources/views/components/settings/layout.blade.php)
  - Appended navigation link visible exclusively to super admins.

---

## Verification Results

- **Command**: `php artisan test --compact`
- **Result**: `Passed` (All 103 tests passed successfully, including 8 new roles & permissions tests)
- **Static Analysis**: `Passed` (PHPStan level 7 reports 0 errors)
- **Formatting**: `Passed` (Pint dirty formatter checked and clean)
