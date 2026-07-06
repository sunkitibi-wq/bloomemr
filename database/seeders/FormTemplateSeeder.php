<?php

namespace Database\Seeders;

use App\Models\FormTemplate;
use Illuminate\Database\Seeder;

class FormTemplateSeeder extends Seeder
{
    public function run(): void
    {
        FormTemplate::create([
            'title' => 'Consent for Treatment',
            'description' => 'General authorization for psychiatric assessment and ongoing pharmacotherapy and therapeutic services.',
            'type' => 'consent',
            'version' => 1,
            'is_active' => true,
            'fields' => [
                [
                    'name' => 'guardian_name',
                    'label' => 'Full Name of Parent/Guardian completing this form',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'name' => 'relationship_to_patient',
                    'label' => 'Relationship to Patient',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'name' => 'agree_to_treatment',
                    'label' => 'I authorize and consent to psychiatric treatment and medication management services for the patient.',
                    'type' => 'checkbox',
                    'required' => true,
                ],
            ],
        ]);

        FormTemplate::create([
            'title' => 'Telehealth Informed Consent',
            'description' => 'Informed consent for the use of interactive video and telecommunications technology for clinical consultations (NYS Article 29-G compliance).',
            'type' => 'consent',
            'version' => 1,
            'is_active' => true,
            'fields' => [
                [
                    'name' => 'nys_resident',
                    'label' => 'I confirm that the patient is physically located in New York State at the time of telehealth sessions.',
                    'type' => 'checkbox',
                    'required' => true,
                ],
                [
                    'name' => 'telehealth_understanding',
                    'label' => 'I understand that telehealth involves secure video/audio communications and carries unique privacy and technical considerations.',
                    'type' => 'checkbox',
                    'required' => true,
                ],
                [
                    'name' => 'emergency_plan',
                    'label' => 'Emergency Plan: In the event of a crisis during a session, I agree to call 911 or go to the nearest emergency room.',
                    'type' => 'checkbox',
                    'required' => true,
                ],
            ],
        ]);

        FormTemplate::create([
            'title' => 'HIPAA Notice of Privacy Practices',
            'description' => 'Acknowledgement of receipt of the HIPAA Notice of Privacy Practices, documenting how your health information may be used and shared.',
            'type' => 'consent',
            'version' => 1,
            'is_active' => true,
            'fields' => [
                [
                    'name' => 'received_notice',
                    'label' => 'I acknowledge that I have received and had the opportunity to review the HIPAA Notice of Privacy Practices.',
                    'type' => 'checkbox',
                    'required' => true,
                ],
                [
                    'name' => 'email_consent',
                    'label' => 'I consent to receive non-encrypted appointment reminders and administrative emails.',
                    'type' => 'checkbox',
                    'required' => false,
                ],
            ],
        ]);
    }
}
