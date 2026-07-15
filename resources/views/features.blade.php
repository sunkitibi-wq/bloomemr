@extends('layouts.frontend')

@section('title', 'GAMUT-C | Features')

@section('content')
<div class="pt-32 pb-24 bg-surface min-h-screen">
    <div class="max-w-5xl mx-auto px-margin-page">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="font-headline-xl text-headline-xl text-trust-navy mb-4">GAMUT-C Features</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-3xl mx-auto">
                GAMUT-C is a comprehensive electronic health records and medical practice management application. It features fully integrated electronic health records, practice management, scheduling, electronic billing, and a whole lot more.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Patient Demographics -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-8 hover:shadow-md transition-shadow">
                <h3 class="font-headline-lg text-headline-lg text-trust-navy mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage">person</span> Patient Demographics</h3>
                <ul class="list-disc list-inside space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>Track patient demographics</li>
                    <li>Primary information (name, date of birth, sex, identification)</li>
                    <li>Contact information of patient and patient's employer</li>
                    <li>Primary provider and HIPAA information</li>
                    <li>Language and ethnicity</li>
                    <li>Insurance coverage tracking</li>
                    <li>Fully Customizable</li>
                </ul>
            </div>

            <!-- Patient Scheduling -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-8 hover:shadow-md transition-shadow">
                <h3 class="font-headline-lg text-headline-lg text-trust-navy mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage">calendar_month</span> Patient Scheduling</h3>
                <ul class="list-disc list-inside space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>Patient Flow Board, Tracking, and Reporting</li>
                    <li>Supports multiple facilities</li>
                    <li>Patient appointment notification via email and sms</li>
                    <li>Recall (reminders) Board</li>
                    <li>Compact and flexible appointment calendar</li>
                    <li>Find open appointment slots</li>
                    <li>Categories and colors for appointment types</li>
                    <li>Repeating appointments</li>
                </ul>
            </div>

            <!-- Electronic Medical Records -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-8 hover:shadow-md transition-shadow">
                <h3 class="font-headline-lg text-headline-lg text-trust-navy mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage">medical_information</span> Electronic Medical Records</h3>
                <ul class="list-disc list-inside space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>Encounters and Medical Issues</li>
                    <li>Medications and Immunizations</li>
                    <li>Vitals (growth charts included)</li>
                    <li>SOAP note and Review of systems</li>
                    <li>Template Driven Forms and Smart Phrases</li>
                    <li>Graphical Charting, Labs, and Procedures</li>
                    <li>Patient Reports and Referrals</li>
                    <li>Electronic digital document management</li>
                    <li>Clinic Messaging and Direct Messaging</li>
                </ul>
            </div>

            <!-- Prescriptions -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-8 hover:shadow-md transition-shadow">
                <h3 class="font-headline-lg text-headline-lg text-trust-navy mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage">prescriptions</span> Prescriptions</h3>
                <ul class="list-disc list-inside space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>Online drug search</li>
                    <li>Track patient prescriptions and medications</li>
                    <li>Create and send prescriptions</li>
                    <li>E-Prescribe via Surescripts Integration</li>
                    <li>Print, Fax, and Email capabilities</li>
                    <li>In-house pharmacy dispensary support</li>
                </ul>
            </div>

            <!-- Medical Billing -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-8 hover:shadow-md transition-shadow">
                <h3 class="font-headline-lg text-headline-lg text-trust-navy mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage">receipt_long</span> Medical Billing</h3>
                <ul class="list-disc list-inside space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>Flexible system of coding including CPT, HCPCS, ICD9, ICD10 and SNOMED codes</li>
                    <li>Support for electronic billing to clearinghouses</li>
                    <li>Support for paper claims</li>
                    <li>Medical claim management interface</li>
                    <li>Insurance Eligibility Queries</li>
                    <li>Accounts Receivable Interface</li>
                    <li>EOB Entry Interface</li>
                </ul>
            </div>

            <!-- Patient Portal -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-8 hover:shadow-md transition-shadow">
                <h3 class="font-headline-lg text-headline-lg text-trust-navy mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage">devices</span> Patient Portal</h3>
                <ul class="list-disc list-inside space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>Modern User Interface</li>
                    <li>Scheduling and Appointments</li>
                    <li>Secure Messaging and Chat</li>
                    <li>Online Payments</li>
                    <li>Customized Forms and New Patient Registration</li>
                    <li>Reports, Labs, Medical Problems</li>
                    <li>Medications and Allergies</li>
                </ul>
            </div>

            <!-- Clinical Decision Rules & Reports -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-8 hover:shadow-md transition-shadow">
                <h3 class="font-headline-lg text-headline-lg text-trust-navy mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage">rule</span> Rules & Reports</h3>
                <ul class="list-disc list-inside space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>Physician and Patient Reminders</li>
                    <li>Clinical Quality Measure (CQM) Calculations</li>
                    <li>Automated Measure Calculations (AMC) and Tracking</li>
                    <li>Appointments and Encounters reporting</li>
                    <li>Patient Lists and Referrals</li>
                    <li>Syndromic Surveillance</li>
                    <li>Sales, Collections, and Insurance Distributions</li>
                </ul>
            </div>

            <!-- Security & Support -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-8 hover:shadow-md transition-shadow">
                <h3 class="font-headline-lg text-headline-lg text-trust-navy mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage">security</span> Security</h3>
                <ul class="list-disc list-inside space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>HIPAA Compliant Platform</li>
                    <li>Support for Role Based Menus and Custom Menus</li>
                    <li>Ability to Encrypt Patient Documents</li>
                    <li>Supports fine-grained per-user access controls</li>
                    <li>Database Connection Encryption Support</li>
                    <li>Remotely accessible from any modern web browser with a suitable security certificate installed</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
