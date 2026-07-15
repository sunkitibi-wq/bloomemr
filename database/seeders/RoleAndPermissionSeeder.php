<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view_patients',
            'create_patients',
            'update_patients',
            'delete_patients',

            'view_encounters',
            'create_encounters',
            'update_encounters',
            'delete_encounters',
            'sign_encounters',

            'view_documents',
            'create_documents',
            'delete_documents',

            'view_prescriptions',
            'update_prescriptions',

            'view_clinical_notes',
            'create_clinical_notes',
            'update_clinical_notes',
            'delete_clinical_notes',
            'sign_clinical_notes',

            'manage_users',

            // New EHR/EMR role-specific permissions
            'view_lab_orders',
            'manage_lab_orders',
            'enter_lab_results',

            'view_imaging_requests',
            'manage_imaging_requests',
            'enter_imaging_reports',

            'view_billing',
            'manage_billing',

            'manage_patient_records',

            'verify_insurance_coverage',
            'review_insurance_claims',

            'view_public_health_reports',

            // Pharmacy-specific
            'manage_inventory',
            'view_pharmacy_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Define roles and assign permissions

        // Doctor / Attending
        $doctorRole = Role::findOrCreate('doctor');
        $attendingRole = Role::findOrCreate('attending');
        $doctorPermissions = [
            'view_patients', 'create_patients', 'update_patients',
            'view_encounters', 'create_encounters', 'update_encounters', 'sign_encounters',
            'view_documents', 'create_documents',
            'view_prescriptions', 'update_prescriptions',
            'view_clinical_notes', 'create_clinical_notes', 'update_clinical_notes', 'sign_clinical_notes',
            'view_lab_orders', 'manage_lab_orders',
            'view_imaging_requests', 'manage_imaging_requests',
            'manage_inventory',
        ];
        $doctorRole->syncPermissions($doctorPermissions);
        $attendingRole->syncPermissions($doctorPermissions);

        // Resident
        $residentRole = Role::findOrCreate('resident');
        $residentRole->syncPermissions([
            'view_patients', 'create_patients', 'update_patients',
            'view_encounters', 'create_encounters', 'update_encounters',
            'view_documents', 'create_documents',
            'view_clinical_notes', 'create_clinical_notes', 'update_clinical_notes',
            'view_lab_orders',
            'view_imaging_requests',
        ]);

        // Nurse / Clinical Staff
        $nurseRole = Role::findOrCreate('nurse');
        $staffRole = Role::findOrCreate('clinical_staff');
        $nursePermissions = [
            'view_patients', 'create_patients', 'update_patients',
            'view_encounters', 'create_encounters',
            'view_documents', 'create_documents',
            'view_clinical_notes', 'create_clinical_notes',
            'view_lab_orders',
            'view_imaging_requests',
            'manage_inventory',
        ];
        $nurseRole->syncPermissions($nursePermissions);
        $staffRole->syncPermissions($nursePermissions);

        // Receptionist
        $receptionistRole = Role::findOrCreate('receptionist');
        $receptionistRole->syncPermissions([
            'view_patients', 'create_patients', 'update_patients',
        ]);

        // Laboratory Officer / Technician
        $labRole = Role::findOrCreate('laboratory_officer');
        $labRole->syncPermissions([
            'view_patients',
            'view_lab_orders',
            'enter_lab_results',
        ]);

        // Pharmacist
        $pharmacistRole = Role::findOrCreate('pharmacist');
        $pharmacistRole->syncPermissions([
            // Core prescription workflow
            'view_prescriptions',
            'update_prescriptions',

            // Patient look-up (read-only) to verify allergies, DOB, insurance
            'view_patients',

            // Medication inventory & stock management
            'manage_inventory',

            // Pharmacy settings (manage linked pharmacy records)
            'view_pharmacy_settings',
        ]);

        // Cashier / Billing Admin / Billing Officer
        $cashierRole = Role::findOrCreate('cashier');
        $billingRole = Role::findOrCreate('billing_admin');
        $cashierPermissions = [
            'view_patients',
            'view_billing',
            'manage_billing',
            'review_insurance_claims',
        ];
        $cashierRole->syncPermissions($cashierPermissions);
        $billingRole->syncPermissions($cashierPermissions);

        // Medical Records Officer
        $mroRole = Role::findOrCreate('medical_records_officer');
        $mroRole->syncPermissions([
            'view_patients',
            'manage_patient_records',
        ]);

        // Radiologist
        $radiologistRole = Role::findOrCreate('radiologist');
        $radiologistRole->syncPermissions([
            'view_patients',
            'view_imaging_requests',
            'enter_imaging_reports',
        ]);

        // Insurance Officer
        $insuranceRole = Role::findOrCreate('insurance_officer');
        $insuranceRole->syncPermissions([
            'verify_insurance_coverage',
            'review_insurance_claims',
        ]);

        // Public Health Officer / Agency
        $publicHealthRole = Role::findOrCreate('public_health_officer');
        $publicHealthRole->syncPermissions([
            'view_public_health_reports',
        ]);

        // Hospital Administrator
        $adminRole = Role::findOrCreate('hospital_administrator');
        $adminRole->syncPermissions($permissions);

        // Guardian (Patient/Family Portal users – no clinical access)
        Role::findOrCreate('guardian');

        // Super Admin
        $superAdminRole = Role::findOrCreate('super_admin');
        $superAdminRole->syncPermissions($permissions);
    }
}
