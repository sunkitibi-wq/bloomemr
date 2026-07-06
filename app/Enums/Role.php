<?php

namespace App\Enums;

enum Role: string
{
    case Attending = 'attending';
    case Resident = 'resident';
    case ClinicalStaff = 'clinical_staff';
    case BillingAdmin = 'billing_admin';
    case SuperAdmin = 'super_admin';
    case Guardian = 'guardian';

    // New typical EHR/EMR roles
    case Doctor = 'doctor';
    case Nurse = 'nurse';
    case Receptionist = 'receptionist';
    case LaboratoryOfficer = 'laboratory_officer';
    case Cashier = 'cashier';
    case MedicalRecordsOfficer = 'medical_records_officer';
    case Radiologist = 'radiologist';
    case InsuranceOfficer = 'insurance_officer';
    case PublicHealthOfficer = 'public_health_officer';
    case HospitalAdministrator = 'hospital_administrator';

    public function label(): string
    {
        return match ($this) {
            self::Attending => 'Attending Physician / NP',
            self::Resident => 'Resident / Fellow',
            self::ClinicalStaff => 'Clinical Staff / MA',
            self::BillingAdmin => 'Billing / Admin',
            self::SuperAdmin => 'Super Admin',
            self::Guardian => 'Guardian / Family Member',
            self::Doctor => 'Physician / Doctor',
            self::Nurse => 'Nurse',
            self::Receptionist => 'Receptionist / Front Desk',
            self::LaboratoryOfficer => 'Laboratory Officer / Technician',
            self::Cashier => 'Billing Officer / Cashier',
            self::MedicalRecordsOfficer => 'Medical Records Officer',
            self::Radiologist => 'Radiologist',
            self::InsuranceOfficer => 'Insurance Officer',
            self::PublicHealthOfficer => 'Public Health Officer / Agency',
            self::HospitalAdministrator => 'Hospital Administrator',
        };
    }

    public function canPrescribe(): bool
    {
        return in_array($this, [self::Attending, self::Doctor, self::Resident, self::SuperAdmin, self::HospitalAdministrator]);
    }

    public function requiresCoSign(): bool
    {
        return $this === self::Resident;
    }

    public function canAccessClinicalNotes(): bool
    {
        return match ($this) {
            self::BillingAdmin,
            self::Guardian,
            self::Receptionist,
            self::Cashier,
            self::InsuranceOfficer,
            self::PublicHealthOfficer => false,
            default => true,
        };
    }
}
