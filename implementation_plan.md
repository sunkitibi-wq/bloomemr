# Implementation Plan - Registration Fields & Portal User Association

We will support the custom registration fields (`clinician`/`guardian` role and `child_name`) and properly associate the registered parent/guardian user with the patient (child) record via a `portal_user_id` foreign key.

## Proposed Changes

### Database Layer

#### [NEW] [2026_07_02_123000_add_portal_user_id_to_patients_table.php](file:///c:/Users/User/Herd/bloom/database/migrations/2026_07_02_123000_add_portal_user_id_to_patients_table.php)
- Add a nullable `portal_user_id` foreign key pointing to the `users` table on the `patients` table.

---

### EMR Application Layer

#### [MODIFY] [Patient.php](file:///c:/Users/User/Herd/bloom/app/Models/Patient.php)
- Add `portal_user_id` to `$fillable`.
- Define a `portalUser` relationship pointing to the `User` model.

#### [MODIFY] [User.php](file:///c:/Users/User/Herd/bloom/app/Models/User.php)
- Define a `portalPatients` relationship pointing to the `Patient` model.
- Add `portal_user_id` to the property types list in the PHPDoc.

#### [MODIFY] [CreateNewUser.php](file:///c:/Users/User/Herd/bloom/app/Actions/Fortify/CreateNewUser.php)
- Update `role` validation to accept `clinician`, `guardian`, and `attending`.
- Map the submitted `clinician` role to the database-recognized `attending` role.
- Set the user's `practice_id` on registration to avoid null practice references.
- Link the created `Patient` record to the new user by saving `portal_user_id => $user->id`.

#### [MODIFY] [PatientPortalDashboard.php](file:///c:/Users/User/Herd/bloom/app/Livewire/Portal/PatientPortalDashboard.php)
- Update the patient lookup to retrieve records based on `portal_user_id` instead of matching by email.

---

## Verification Plan

### Automated Tests
- Run `php artisan test --filter=RegistrationTest`
- Run `php artisan test --filter=PatientPortalTest`
- Create new test cases in `RegistrationTest.php` to verify:
  1. A clinician registers and gets the `attending` role, and has `practice_id` set.
  2. A guardian registers with `child_name`, gets the `guardian` role, has `practice_id` set, and creates a child `Patient` record associated with the user's ID via `portal_user_id`.

### Code Formatting
- Run `vendor/bin/pint --format agent`
- Run `vendor/bin/phpstan analyse --memory-limit=1G`
