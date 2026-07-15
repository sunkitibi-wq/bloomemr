<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>GAMUT-C | Features</title>
    
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    @fonts

    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&family=Inter:wght@400;600&family=JetBrains+Mono&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-growth-sage/30">
<!-- Top Navigation Bar -->
<header class="fixed top-0 w-full z-50 bg-surface shadow-sm transition-all duration-300 h-20 flex items-center">
    <nav class="flex justify-between items-center px-margin-page w-full max-w-7xl mx-auto">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="material-symbols-outlined text-trust-navy text-3xl" style="font-variation-settings: 'FILL' 1;">spa</span>
            <span class="font-headline-md text-headline-md font-bold text-trust-navy">GAMUT-C</span>
        </a>
        <div class="hidden md:flex gap-8 items-center">
            <a class="font-body-md text-body-md text-trust-navy font-semibold transition-colors" href="{{ route('features') }}">Features</a>
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('home') }}#modules">Modules</a>
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('demo') }}">Demo</a>
            
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-trust-navy text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md transition-all active:scale-95 hover:bg-trust-navy/90 shadow-sm">
                        {{ __('Go to Dashboard') }}
                    </a>
                @else
                    <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('login') }}">{{ __('Log in') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-trust-navy text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md transition-all active:scale-95 hover:bg-trust-navy/90 shadow-sm">
                            {{ __('Register') }}
                        </a>
                    @endif
                @endauth
            @endif
        </div>
        <div class="md:hidden">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-trust-navy">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-trust-navy">Log in</a>
                @endauth
            @endif
        </div>
    </nav>
</header>
<main class="pt-32 pb-24 bg-surface min-h-screen">
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
</main>
<!-- Footer -->
<footer class="bg-trust-navy text-on-primary w-full">
<div class="py-16 px-margin-page grid grid-cols-1 md:grid-cols-4 gap-gutter max-w-7xl mx-auto">
<div class="md:col-span-1 space-y-6">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-growth-sage text-2xl" style="font-variation-settings: 'FILL' 1;">spa</span>
<span class="font-headline-sm text-headline-sm font-bold text-on-primary">Bloom</span>
</div>
<p class="font-body-sm text-body-sm opacity-70 leading-relaxed">
                    Designed for practitioners, by practitioners. Leading the evolution of pediatric EMR systems.
                </p>
</div>
<div class="space-y-4">
<h4 class="font-label-md text-label-md uppercase tracking-widest text-growth-sage">Product</h4>
<nav class="flex flex-col gap-2">
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="{{ route('features') }}">Features</a>
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="{{ route('home') }}#modules">Modules</a>
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="{{ route('demo') }}">Demo</a>
</nav>
</div>
<div class="space-y-4">
<h4 class="font-label-md text-label-md uppercase tracking-widest text-growth-sage">Compliance</h4>
<nav class="flex flex-col gap-2">
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#">HIPAA Standards</a>
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#">Privacy Policy</a>
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#">Security Audit</a>
</nav>
</div>
<div class="space-y-4">
<h4 class="font-label-md text-label-md uppercase tracking-widest text-growth-sage">Resources</h4>
<nav class="flex flex-col gap-2">
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#">Help Center</a>
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#">Clinical Guides</a>
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#">System Status</a>
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#">Contact Support</a>
</nav>
</div>
</div>
<div class="border-t border-on-primary/10 py-8 px-margin-page">
<div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
<p class="font-body-sm text-body-sm opacity-60">
                    © 2026 GAMUT-C. All rights reserved. HIPAA Compliant.
                </p>
</div>
</div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.querySelector('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.add('shadow-md', 'bg-surface/95', 'backdrop-blur-md');
            } else {
                header.classList.remove('shadow-md', 'bg-surface/95', 'backdrop-blur-md');
            }
        });
    });
</script>
</body>
</html>
