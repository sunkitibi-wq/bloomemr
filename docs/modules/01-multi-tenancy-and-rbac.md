# Module 01: Multi-Tenancy, Practice Hierarchies & RBAC

## 1. Executive Overview

Bloom EMR is engineered as a multi-tenant healthcare platform. It provides strict tenant-level data isolation across independent healthcare organizations (hospitals, outpatient clinics, and community pharmacies), sub-facilities, and departments. 

The architecture guarantees:
- **HIPAA-Compliant Isolation**: Complete logical data partitioning preventing cross-practice Protected Health Information (PHI) exposure.
- **Hierarchical Practice Structures**: Support for parent practices, physical facilities (buildings/locations), and departmental units (e.g., Pediatrics, Emergency, Psychiatry).
- **Comprehensive RBAC**: Role-based access controls encompassing clinical providers, diagnostics personnel, billing officers, administrators, and patient guardians.
- **Enterprise Onboarding & KYC**: Practice subscription management, identity verification (KYC), and automated feature tiering.

---

## 2. Personas & Access Control

Role definitions are managed via the [Role](file:///c:/Users/User/Herd/bloom/app/Enums/Role.php) enum and enhanced by [Spatie Permissions](file:///c:/Users/User/Herd/bloom/composer.json#L23).

| Role Key | Display Name | Clinical Access | Prescribing Auth | Supervised / Co-Sign Required |
| :--- | :--- | :---: | :---: | :---: |
| `super_admin` | Super Admin / System Admin | System-wide | Yes | No |
| `hospital_administrator` | Hospital Administrator | Practice-wide | Yes | No |
| `attending` | Attending Physician / NP | Full Clinical | Yes | No |
| `resident` | Resident / Fellow | Supervised Clinical | Yes | **Yes** (attending must co-sign) |
| `doctor` | Physician / Doctor | Full Clinical | Yes | No |
| `nurse` | Staff Nurse | Nursing / Vitals / Med Admin | No | No |
| `clinical_staff` | Clinical Staff / Medical Assistant | Vitals / Triage / Forms | No | No |
| `laboratory_officer` | Laboratory Officer / Technician | Lab Orders / Results Entry | No | No |
| `radiologist` | Radiologist | Imaging Orders / DICOM PACS | No | No |
| `billing_admin` | Billing Administrator | Claims / Invoices / Financials | No | No |
| `cashier` | Billing Officer / Cashier | POS Payments / Invoices | No | No |
| `insurance_officer` | Insurance Officer | Eligibility / Clearinghouse | No | No |
| `receptionist` | Receptionist / Front Desk | Scheduling / Flow Board / Intake | No | No |
| `medical_records_officer` | Medical Records Officer | Charts / Documents / Audits | No | No |
| `pharmacist` | Pharmacist | Pharmacy Portal / Dispensing | No | No |
| `guardian` | Patient / Family Guardian | Patient Portal (Self & Dependents) | No | No |
| `public_health_officer` | Public Health Officer / Agency | De-identified Epidemiology / CQMs | No | No |

---

## 3. Database Architecture & Schema

### Primary Models
- [Practice](file:///c:/Users/User/Herd/bloom/app/Models/Practice.php): The root tenant entity (`type`: `hospital`, `clinic`, or `pharmacy`; `is_active`, `is_enterprise`, `settings`).
- [Facility](file:///c:/Users/User/Herd/bloom/app/Models/Facility.php): Physical branches/campuses belonging to a practice.
- [Department](file:///c:/Users/User/Herd/bloom/app/Models/Department.php): Specialized operational units within a facility (e.g., Outpatient Psychiatry, Inpatient Pediatrics).
- [User](file:///c:/Users/User/Herd/bloom/app/Models/User.php): Practice staff or system users linked via `practice_id`, possessing a primary `role`, provider license details, and KYC status.
- [AuditLog](file:///c:/Users/User/Herd/bloom/app/Models/AuditLog.php): Immutable log of record reads, modifications, authentication events, and data exports.

### Multi-Tenancy Scoping Trait: `BelongsToPractice`
Tenant-scoped models (Patients, Encounters, Invoices, Orders, Prescriptions) utilize [BelongsToPractice](file:///c:/Users/User/Herd/bloom/app/Models/Concerns/BelongsToPractice.php):
- **Global Query Scope**: Automatically injects `WHERE practice_id = ?` based on the active authenticated user or the container-bound `current_practice`.
- **Model Creating Hook**: Automatically assigns `$model->practice_id` upon creation if not manually specified.

```php
// app/Models/Concerns/BelongsToPractice.php
public static function bootBelongsToPractice(): void
{
    static::addGlobalScope(new PracticeScope);

    static::creating(function ($model) {
        if ($model->practice_id) return;
        $practice = app()->bound('current_practice') ? app('current_practice') : null;
        if ($practice) {
            $model->practice_id = $practice->id;
        } elseif (auth()->check() && auth()->user()->practice_id) {
            $model->practice_id = auth()->user()->practice_id;
        }
    });
}
```

---

## 4. Backend Services & Authentication Flow

### Authentication & Redirection: [LoginResponse](file:///c:/Users/User/Herd/bloom/app/Http/Responses/LoginResponse.php)
1. **Fortify Pipeline**: Authenticates credentials, verifies Two-Factor Authentication (TOTP or WebAuthn Passkeys), and checks KYC/subscription status via `kyc.subscribed` middleware.
2. **Dynamic Routing**:
   - `super_admin` / System Admin $\rightarrow$ `/system/dashboard`
   - `hospital_administrator` $\rightarrow$ `/hospital/analytics`
   - `pharmacist` $\rightarrow$ `/pharmacy/portal`
   - `guardian` $\rightarrow$ `/portal` (Patient Portal)
   - Clinical Staff $\rightarrow$ `/dashboard` (resolves to `/hospital/dashboard`)

### KYC & Subscription Middleware
- Managed by `kyc.subscribed` middleware ensuring unverified practices complete business registration and payment setup before clinical modules unlock.

---

## 5. UI Components & Layouts

- **System Admin Portal**:
  - [SystemDashboard](file:///c:/Users/User/Herd/bloom/app/Livewire/SystemAdmin/SystemDashboard.php): Platform-level KPIs, tenant count, system health.
  - [PracticeManager](file:///c:/Users/User/Herd/bloom/app/Livewire/SystemAdmin/PracticeManager.php): Provisioning, activating, and deactivating practices.
  - [KycVerificationManager](file:///c:/Users/User/Herd/bloom/app/Livewire/SystemAdmin/KycVerificationManager.php): Reviewing medical licenses and identity documents.
- **Settings & User Management**:
  - [UserManagement](file:///c:/Users/User/Herd/bloom/app/Livewire/Settings/UserManagement.php): Staff directory, role assignment, credential provisioning.
  - [RolesAndPermissions](file:///c:/Users/User/Herd/bloom/app/Livewire/Settings/RolesAndPermissions.php): Granular capability overrides.
  - [PracticeSettings](file:///c:/Users/User/Herd/bloom/app/Livewire/Settings/PracticeSettings.php): Facility branding, operational hours, practice metadata.

---

## 6. Testing & Quality Assurance

- [MultiTenancyTest](file:///c:/Users/User/Herd/bloom/tests/Feature/MultiTenancyTest.php): Tests global practice scoping, cross-practice query prevention, and automatic `practice_id` attachment.
- [RolesAndPermissionsTest](file:///c:/Users/User/Herd/bloom/tests/Feature/RolesAndPermissionsTest.php): Tests role-based authorization gates, clinical note view restrictions, and prescribing policies.
- [KycAndSubscriptionTest](file:///c:/Users/User/Herd/bloom/tests/Feature/KycAndSubscriptionTest.php): Tests onboarding flows, document verification, and trial/subscription enforcement.
