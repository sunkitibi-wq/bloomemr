<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>GAMUT-C | How It Works</title>
    <meta name="description" content="Learn how GAMUT-C connects clinicians, administrative staff, and patients in a seamless clinical workflow.">

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
        .role-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .role-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(15,23,42,0.12); }
        .step-connector { background: linear-gradient(to right, #E2E8F0, #E2E8F0); }
        .flow-line { stroke-dasharray: 8 4; animation: dash 1.5s linear infinite; }
        @keyframes dash { to { stroke-dashoffset: -36; } }
        .section-fade { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
        .section-fade.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-growth-sage/30">

<!-- Navigation -->
<header class="fixed top-0 w-full z-50 bg-surface shadow-sm transition-all duration-300 h-20 flex items-center">
    <nav class="flex justify-between items-center px-margin-page w-full max-w-7xl mx-auto">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="material-symbols-outlined text-trust-navy text-3xl" style="font-variation-settings: 'FILL' 1;">spa</span>
            <span class="font-headline-md text-headline-md font-bold text-trust-navy">GAMUT-C</span>
        </a>
        <div class="hidden md:flex gap-8 items-center">
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('features') }}">Features</a>
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('home') }}#modules">Modules</a>
            <a class="font-body-md text-body-md text-trust-navy font-semibold transition-colors" href="{{ route('how-it-works') }}">How It Works</a>
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

<main class="pt-28 pb-24 min-h-screen">

    <!-- Hero -->
    <section class="bg-gradient-to-br from-trust-navy via-slate-800 to-slate-900 text-white py-20 px-margin-page">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 border border-white/20 rounded-full text-xs font-bold uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">menu_book</span>
                User Guide & Manual
            </div>
            <h1 class="font-headline-xl text-headline-xl font-bold mb-4 leading-tight">How GAMUT-C Works</h1>
            <p class="font-body-lg text-body-lg opacity-80 max-w-2xl mx-auto">
                A complete guide to how clinicians, administrative staff, and patients interact within the GAMUT-C clinical ecosystem — from first login to patient discharge.
            </p>
            <!-- Jump links -->
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <a href="#overview" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full text-sm font-medium transition-colors">System Overview</a>
                <a href="#roles" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full text-sm font-medium transition-colors">User Roles</a>
                <a href="#workflows" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full text-sm font-medium transition-colors">Workflows</a>
                <a href="#patient-journey" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full text-sm font-medium transition-colors">Patient Journey</a>
                <a href="#pricing" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full text-sm font-medium transition-colors">Licensing & Billing</a>
                <a href="#access" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full text-sm font-medium transition-colors">Access & Security</a>
            </div>
        </div>
    </section>

    <!-- System Overview -->
    <section id="overview" class="py-20 px-margin-page bg-surface section-fade">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy font-bold">System Overview</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2 max-w-2xl mx-auto">GAMUT-C is a multi-role clinical management platform. Each user type interacts with a tailored interface — the right tools, for the right people, at the right time.</p>
            </div>
            <!-- Architecture diagram (visual boxes) -->
            <div class="relative flex flex-col md:flex-row items-center justify-center gap-6">
                <!-- Clinical Staff -->
                <div class="flex-1 bg-trust-navy/5 border border-trust-navy/20 rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-trust-navy/10 flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-trust-navy text-3xl" style="font-variation-settings: 'FILL' 1;">stethoscope</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-trust-navy font-bold">Clinical Staff</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Doctors, Nurses, Residents</p>
                    <ul class="mt-3 text-left text-xs space-y-1 text-on-surface-variant">
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Patient records & notes</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Encounter documentation</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Orders & prescriptions</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Scheduling & telehealth</li>
                    </ul>
                </div>
                <!-- Arrow -->
                <div class="hidden md:flex flex-col items-center gap-1 text-on-surface-variant/40">
                    <span class="material-symbols-outlined text-3xl">sync_alt</span>
                    <span class="text-xs font-medium">GAMUT-C Core</span>
                </div>
                <!-- Admin Staff -->
                <div class="flex-1 bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-amber-600 text-3xl" style="font-variation-settings: 'FILL' 1;">admin_panel_settings</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-trust-navy font-bold">Administrative Staff</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Billing, Reception, Records</p>
                    <ul class="mt-3 text-left text-xs space-y-1 text-on-surface-variant">
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Claims & billing management</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Appointment scheduling</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Insurance verification</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Medical records</li>
                    </ul>
                </div>
                <!-- Arrow -->
                <div class="hidden md:flex flex-col items-center gap-1 text-on-surface-variant/40">
                    <span class="material-symbols-outlined text-3xl">sync_alt</span>
                    <span class="text-xs font-medium">Patient Portal</span>
                </div>
                <!-- Patients -->
                <div class="flex-1 bg-growth-sage/5 border border-growth-sage/30 rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-growth-sage/10 flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-growth-sage text-3xl" style="font-variation-settings: 'FILL' 1;">family_restroom</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-trust-navy font-bold">Patients & Families</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Guardians & Patients</p>
                    <ul class="mt-3 text-left text-xs space-y-1 text-on-surface-variant">
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> View health records</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Book & track appointments</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Message care team</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Pay bills & request refills</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- User Roles -->
    <section id="roles" class="py-20 px-margin-page bg-surface-container-lowest section-fade">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy font-bold">User Roles & Access</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Every user in GAMUT-C is assigned a specific role. Each role determines what menus, data, and actions are available to that person.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                @php
                    $roles = [
                        ['icon' => 'stethoscope', 'color' => 'trust-navy', 'bg' => 'trust-navy/5', 'border' => 'trust-navy/20', 'label' => 'Doctor / Attending', 'route_label' => 'Staff Login → /login', 'desc' => 'Full clinical access including encounter notes, diagnoses, orders, prescriptions, and referrals. Leads the care team.'],
                        ['icon' => 'medical_services', 'color' => 'trust-navy', 'bg' => 'trust-navy/5', 'border' => 'trust-navy/20', 'label' => 'Resident Physician', 'route_label' => 'Staff Login → /login', 'desc' => 'Same clinical tools as attending but requires supervision. Encounter notes may require co-sign from an attending physician.'],
                        ['icon' => 'health_and_safety', 'color' => 'blue-600', 'bg' => 'blue-50', 'border' => 'blue-200', 'label' => 'Nurse / Clinical Staff', 'route_label' => 'Staff Login → /login', 'desc' => 'Triages patients, records vitals, assists with encounters, and manages care coordination tasks from the flow board.'],
                        ['icon' => 'desk', 'color' => 'purple-600', 'bg' => 'purple-50', 'border' => 'purple-200', 'label' => 'Receptionist', 'route_label' => 'Staff Login → /login', 'desc' => 'Manages appointments, patient check-ins, incoming calls, and coordinates scheduling between clinicians and patients.'],
                        ['icon' => 'biotech', 'color' => 'cyan-700', 'bg' => 'cyan-50', 'border' => 'cyan-200', 'label' => 'Laboratory Officer', 'route_label' => 'Staff Login → /login', 'desc' => 'Receives lab orders, records test results, and automatically notifies the ordering clinician when results are ready.'],
                        ['icon' => 'medication', 'color' => 'indigo-600', 'bg' => 'indigo-50', 'border' => 'indigo-200', 'label' => 'Pharmacist', 'route_label' => 'Staff Login → /login', 'desc' => 'Reviews electronic prescriptions, manages the drug inventory, and processes medication refill requests from the patient portal.'],
                        ['icon' => 'receipt_long', 'color' => 'amber-700', 'bg' => 'amber-50', 'border' => 'amber-200', 'label' => 'Billing Admin', 'route_label' => 'Staff Login → /login', 'desc' => 'Manages the revenue cycle — submitting insurance claims, processing ERA/EOBs, handling patient balances, and resolving denials.'],
                        ['icon' => 'folder_open', 'color' => 'orange-600', 'bg' => 'orange-50', 'border' => 'orange-200', 'label' => 'Medical Records Officer', 'route_label' => 'Staff Login → /login', 'desc' => 'Manages patient document uploads, release of records, and ensures medical records are correctly filed and retrievable.'],
                        ['icon' => 'radiology', 'color' => 'slate-600', 'bg' => 'slate-50', 'border' => 'slate-200', 'label' => 'Radiologist', 'route_label' => 'Staff Login → /login', 'desc' => 'Receives imaging orders, uploads radiology reports, and communicates findings back to the ordering clinician within the EMR.'],
                        ['icon' => 'verified_user', 'color' => 'emerald-700', 'bg' => 'emerald-50', 'border' => 'emerald-200', 'label' => 'Insurance Officer', 'route_label' => 'Staff Login → /login', 'desc' => 'Handles prior authorizations, payer eligibility checks, and coordinates with billing for insurance-related denials.'],
                        ['icon' => 'public', 'color' => 'teal-700', 'bg' => 'teal-50', 'border' => 'teal-200', 'label' => 'Public Health Officer', 'route_label' => 'Staff Login → /login', 'desc' => 'Accesses population health dashboards, monitors reportable disease trends, and manages community health programs.'],
                        ['icon' => 'manage_accounts', 'color' => 'rose-700', 'bg' => 'rose-50', 'border' => 'rose-200', 'label' => 'Hospital Administrator', 'route_label' => 'Staff Login → /login', 'desc' => 'Oversees practice settings, manages user accounts and roles, reviews analytics dashboards, and configures workflows.'],
                        ['icon' => 'family_restroom', 'color' => 'growth-sage', 'bg' => 'growth-sage/5', 'border' => 'growth-sage/30', 'label' => 'Patient / Guardian', 'route_label' => 'Patient Portal → /patient/login', 'desc' => 'Accesses the dedicated Patient Portal to view health records, book appointments, message the care team, request refills, and pay bills.'],
                    ];
                @endphp

                @foreach($roles as $role)
                <div class="role-card bg-{{ $role['bg'] }} border border-{{ $role['border'] }} rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/80 flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-{{ $role['color'] }} text-xl" style="font-variation-settings: 'FILL' 1;">{{ $role['icon'] }}</span>
                    </div>
                    <div>
                        <h3 class="font-label-md text-label-md font-bold text-trust-navy">{{ $role['label'] }}</h3>
                        <span class="inline-block text-[10px] font-mono px-2 py-0.5 bg-white/70 rounded-full border border-zinc-200 text-zinc-500 mt-0.5 mb-2">{{ $role['route_label'] }}</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">{{ $role['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Clinical Workflows -->
    <section id="workflows" class="py-20 px-margin-page bg-surface section-fade">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy font-bold">Core Clinical Workflows</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">The most common end-to-end processes in GAMUT-C — step by step.</p>
            </div>

            <!-- Workflow 1: Patient Visit -->
            <div class="mb-12">
                <h3 class="font-headline-md text-headline-md text-trust-navy font-bold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-trust-navy" style="font-variation-settings: 'FILL' 1;">local_hospital</span>
                    Patient Visit Workflow
                </h3>
                <div class="relative">
                    <div class="flex flex-col gap-0">
                        @php
                            $visitSteps = [
                                ['step' => '1', 'role' => 'Receptionist', 'color' => 'purple', 'action' => 'Check-In & Schedule', 'detail' => 'Receptionist verifies insurance eligibility, checks the patient in on the scheduling flow board, and assigns them to the correct clinician.'],
                                ['step' => '2', 'role' => 'Nurse / Clinical Staff', 'color' => 'blue', 'action' => 'Triage & Vitals', 'detail' => 'Nurse triages the patient, records vitals (BP, weight, temperature) and updates the chief complaint directly in the encounter.'],
                                ['step' => '3', 'role' => 'Doctor / Attending', 'color' => 'trust-navy', 'action' => 'Consultation & Encounter Note', 'detail' => 'Physician reviews history, conducts examination, and documents the encounter using structured notes with Smart Phrases for speed.'],
                                ['step' => '4', 'role' => 'Doctor / Attending', 'color' => 'trust-navy', 'action' => 'Orders & Prescriptions', 'detail' => 'Physician places lab orders, imaging requests, and sends prescriptions electronically to the pharmacy.'],
                                ['step' => '5', 'role' => 'Laboratory / Pharmacist', 'color' => 'cyan', 'action' => 'Fulfil Orders', 'detail' => 'Lab officer processes test requests and uploads results. Pharmacist reviews and dispenses the prescription.'],
                                ['step' => '6', 'role' => 'Billing Admin', 'color' => 'amber', 'action' => 'Claim Submission', 'detail' => 'Billing admin reviews the encounter, creates a claim with the correct diagnosis and procedure codes, and submits it to the payer.'],
                                ['step' => '7', 'role' => 'Patient / Guardian', 'color' => 'growth-sage', 'action' => 'Portal Follow-up', 'detail' => 'Patient receives a visit summary via the Patient Portal, reviews their bill, requests prescription refills, and books a follow-up appointment.'],
                            ];
                        @endphp
                        @foreach($visitSteps as $i => $s)
                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full bg-trust-navy text-white flex items-center justify-center text-sm font-bold shrink-0 z-10">{{ $s['step'] }}</div>
                                @if(!$loop->last)
                                    <div class="w-px flex-1 min-h-[40px] bg-trust-navy/20 my-1"></div>
                                @endif
                            </div>
                            <div class="pb-8 flex-1">
                                <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-xl p-4 hover:shadow-sm transition-shadow">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 bg-trust-navy/10 text-trust-navy rounded-full">{{ $s['role'] }}</span>
                                    </div>
                                    <h4 class="font-label-md text-label-md font-bold text-trust-navy">{{ $s['action'] }}</h4>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 leading-relaxed">{{ $s['detail'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Workflow 2: Billing Cycle -->
            <div>
                <h3 class="font-headline-md text-headline-md text-trust-navy font-bold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-600" style="font-variation-settings: 'FILL' 1;">payments</span>
                    Billing & Revenue Cycle
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $billingSteps = [
                            ['icon' => 'description', 'title' => 'Encounter Closes', 'desc' => 'Once the physician finalises their encounter note, the visit is marked complete and automatically queued for coding review.'],
                            ['icon' => 'find_in_page', 'title' => 'Coding Review', 'desc' => 'Billing admin reviews diagnosis (ICD-10) and procedure codes (CPT), checks for modifiers, and ensures compliance before submission.'],
                            ['icon' => 'send', 'title' => 'Claim Submitted', 'desc' => 'A CMS-1500 or EDI 837 claim is generated and submitted electronically to the appropriate insurance payer or clearinghouse.'],
                            ['icon' => 'account_balance', 'title' => 'ERA / EOB Posted', 'desc' => 'Electronic Remittance Advice is received and automatically matched to claims. Payments are posted and adjustments applied.'],
                            ['icon' => 'warning', 'title' => 'Denial Management', 'desc' => 'Any denied claims trigger an alert. Billing admin reviews the denial reason, corrects the issue, and resubmits within the payer\'s deadline.'],
                            ['icon' => 'receipt', 'title' => 'Patient Balance', 'desc' => 'Remaining patient responsibilities (copays, deductibles) appear on the Patient Portal for online payment or payment plan setup.'],
                        ];
                    @endphp
                    @foreach($billingSteps as $bs)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-amber-700 text-xl" style="font-variation-settings: 'FILL' 1;">{{ $bs['icon'] }}</span>
                        </div>
                        <h4 class="font-label-md text-label-md font-bold text-trust-navy mb-1">{{ $bs['title'] }}</h4>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">{{ $bs['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Patient Journey -->
    <section id="patient-journey" class="py-20 px-margin-page bg-surface-container-lowest section-fade">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy font-bold">The Patient & Family Journey</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">From first registration to ongoing care — here is exactly how a patient interacts with GAMUT-C.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $journey = [
                        ['step' => '1', 'icon' => 'app_registration', 'title' => 'Register on the Patient Portal', 'desc' => 'A parent or guardian visits /patient/register, fills in their details and their child\'s name, then completes the HIPAA consent step. Their account is created and linked to the clinic.', 'cta' => 'Go to Registration', 'route' => 'patient.register'],
                        ['step' => '2', 'icon' => 'login', 'title' => 'Sign In to the Portal', 'desc' => 'Guardians log in via the dedicated /patient/login page. This routes them through the Patient Portal guard, giving them access only to their family\'s health data.', 'cta' => 'Patient Login', 'route' => 'patient.login'],
                        ['step' => '3', 'icon' => 'calendar_month', 'title' => 'Book & Manage Appointments', 'desc' => 'From the portal dashboard, guardians can request new appointments, view upcoming visits, receive reminders, and join telehealth sessions.', 'cta' => null, 'route' => null],
                        ['step' => '4', 'icon' => 'folder_shared', 'title' => 'Access Health Records', 'desc' => 'After each visit, patients can view their visit summaries, lab results, radiology reports, and clinical notes directly in the portal — no phone calls needed.', 'cta' => null, 'route' => null],
                        ['step' => '5', 'icon' => 'chat', 'title' => 'Message the Care Team', 'desc' => 'Guardians can securely message their care team with non-urgent questions. Messages are routed to the correct clinician or department.', 'cta' => null, 'route' => null],
                        ['step' => '6', 'icon' => 'medication_liquid', 'title' => 'Request Prescription Refills', 'desc' => 'Patients can submit refill requests directly through the portal. The pharmacist reviews and processes these from the Pharmacy Portal.', 'cta' => null, 'route' => null],
                        ['step' => '7', 'icon' => 'credit_card', 'title' => 'View & Pay Bills', 'desc' => 'Outstanding balances, EOB summaries, and payment history are all accessible in the billing section of the patient portal with online payment support.', 'cta' => null, 'route' => null],
                        ['step' => '8', 'icon' => 'upload_file', 'title' => 'Upload Documents & Forms', 'desc' => 'Guardians can upload insurance cards, consent forms, referral letters, and school health forms directly to their patient profile.', 'cta' => null, 'route' => null],
                    ];
                @endphp
                @foreach($journey as $j)
                <div class="bg-surface border border-outline-variant/30 rounded-2xl p-6 flex items-start gap-4 hover:shadow-sm transition-shadow">
                    <div class="w-10 h-10 rounded-full bg-growth-sage text-white flex items-center justify-center text-sm font-bold shrink-0">{{ $j['step'] }}</div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="material-symbols-outlined text-growth-sage text-lg" style="font-variation-settings: 'FILL' 1;">{{ $j['icon'] }}</span>
                            <h3 class="font-label-md text-label-md font-bold text-trust-navy">{{ $j['title'] }}</h3>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">{{ $j['desc'] }}</p>
                        @if($j['cta'])
                            <a href="{{ route($j['route']) }}" class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-growth-sage hover:underline">
                                {{ $j['cta'] }} <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Licensing & Billing -->
    <section id="pricing" class="py-20 px-margin-page bg-surface section-fade">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy font-bold">{{ __('Licensing & Subscription Models') }}</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2 max-w-2xl mx-auto">
                    {{ __('Bloom offers two flexible ways for healthcare providers to access the EMR platform. Each plan incorporates fully compliant security and data sovereignty policies.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                <!-- Individual Practitioner Subscriptions -->
                <div class="bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-growth-sage/10 text-growth-sage flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">person</span>
                        </div>
                        <h3 class="text-lg font-bold text-trust-navy dark:text-zinc-150">{{ __('Individual Practitioner Subscriptions') }}</h3>
                        <p class="text-xs text-zinc-400 mt-1">{{ __('Billed per clinician workspace.') }}</p>
                        
                        <div class="space-y-4 mt-6 text-sm text-on-surface-variant leading-relaxed">
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-growth-sage text-lg shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <div>
                                    <strong>{{ __('Stripe Integration') }}:</strong> {{ __('Clinicians can subscribe directly using a credit card. Payments are processed securely via Stripe.') }}
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-growth-sage text-lg shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <div>
                                    <strong>{{ __('Duration Tiers') }}:</strong> {{ __('Licensing is flexible, offering 1 Month ($99), 6 Months ($499), or 1 Year ($899) terms.') }}
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-growth-sage text-lg shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <div>
                                    <strong>{{ __('Automatic Expiration Blocks') }}:</strong> {{ __('If a subscription lapses, the user is redirected to the plan selector upon clinical login. Patient portal views for their linked guardians are never interrupted to ensure continuous care.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clinic & Organization Billing -->
                <div class="bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-trust-navy/10 text-trust-navy flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">domain</span>
                        </div>
                        <h3 class="text-lg font-bold text-trust-navy dark:text-zinc-150">{{ __('Organization & Clinic Roster Billing') }}</h3>
                        <p class="text-xs text-zinc-400 mt-1">{{ __('Custom B2B contracts for clinics & hospitals.') }}</p>
                        
                        <div class="space-y-4 mt-6 text-sm text-on-surface-variant leading-relaxed">
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-growth-sage text-lg shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <div>
                                    <strong>{{ __('Direct Contact Setup') }}:</strong> {{ __('Clinic administrators cannot activate group pricing online. They must contact Bloom sales directly to finalize their service agreements.') }}
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-growth-sage text-lg shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <div>
                                    <strong>{{ __('Enterprise B2B Roster') }}:</strong> {{ __('Bloom provisions custom databases and enables enterprise flags. Clinicians joining these practices bypass Stripe payment screens automatically.') }}
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-growth-sage text-lg shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <div>
                                    <strong>{{ __('Custom Endpoint Setup') }}:</strong> {{ __('Enterprise plans support custom PBM, lab routing, HL7/FHIR feeds, and dedicated server options.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Access & Security -->
    <section id="access" class="py-20 px-margin-page bg-trust-navy text-white section-fade">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="font-headline-lg text-headline-lg font-bold">Access, Security & Compliance</h2>
                <p class="font-body-md text-body-md opacity-70 mt-2">GAMUT-C is built around role-based access control (RBAC) and HIPAA-compliant security practices.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <span class="material-symbols-outlined text-growth-sage text-3xl mb-4 block" style="font-variation-settings: 'FILL' 1;">lock</span>
                    <h3 class="font-headline-sm text-headline-sm font-bold mb-2">Role-Based Access Control</h3>
                    <p class="font-body-sm text-body-sm opacity-70 leading-relaxed">Every user is assigned a specific role on registration. Permissions are granular — a Pharmacist can see prescriptions but not billing data. A Receptionist can schedule but not document clinical notes.</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <span class="material-symbols-outlined text-growth-sage text-3xl mb-4 block" style="font-variation-settings: 'FILL' 1;">encrypted</span>
                    <h3 class="font-headline-sm text-headline-sm font-bold mb-2">Separate Authentication Portals</h3>
                    <p class="font-body-sm text-body-sm opacity-70 leading-relaxed">Clinical staff log in at <code class="text-xs bg-white/10 px-1 rounded">/login</code> via the web guard. Patients log in at <code class="text-xs bg-white/10 px-1 rounded">/patient/login</code> via the isolated portal guard, ensuring patient data is fully segregated from staff sessions.</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <span class="material-symbols-outlined text-growth-sage text-3xl mb-4 block" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                    <h3 class="font-headline-sm text-headline-sm font-bold mb-2">HIPAA Compliance & Audit</h3>
                    <p class="font-body-sm text-body-sm opacity-70 leading-relaxed">All data is encrypted in transit and at rest. Patient-facing registration includes explicit HIPAA consent before account creation. Two-factor authentication and passkey support are available for all users.</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <span class="material-symbols-outlined text-growth-sage text-3xl mb-4 block" style="font-variation-settings: 'FILL' 1;">how_to_reg</span>
                    <h3 class="font-headline-sm text-headline-sm font-bold mb-2">Practice Workspaces</h3>
                    <p class="font-body-sm text-body-sm opacity-70 leading-relaxed">When a Doctor or Hospital Administrator registers with a Practice name, a new isolated workspace is automatically provisioned. All data within that practice is tenant-scoped and inaccessible to other practices.</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <span class="material-symbols-outlined text-growth-sage text-3xl mb-4 block" style="font-variation-settings: 'FILL' 1;">passkey</span>
                    <h3 class="font-headline-sm text-headline-sm font-bold mb-2">Passkey Authentication</h3>
                    <p class="font-body-sm text-body-sm opacity-70 leading-relaxed">GAMUT-C supports modern passwordless login via passkeys (WebAuthn) for both clinical staff and patients, providing phishing-resistant authentication with biometric convenience.</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <span class="material-symbols-outlined text-growth-sage text-3xl mb-4 block" style="font-variation-settings: 'FILL' 1;">supervisor_account</span>
                    <h3 class="font-headline-sm text-headline-sm font-bold mb-2">Super Admin & System Admin</h3>
                    <p class="font-body-sm text-body-sm opacity-70 leading-relaxed">The first staff member who creates a practice becomes its <strong>Super Admin</strong> with full practice-level control. A separate <strong>System Admin</strong> role manages the overall GAMUT-C platform across all practices.</p>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-12 flex flex-wrap gap-4 justify-center">
                <a href="{{ route('login') }}" class="flex items-center gap-2 px-6 py-3 bg-white text-trust-navy rounded-xl font-semibold text-sm hover:bg-zinc-50 transition-colors">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">medical_services</span>
                    Staff Login
                </a>
                <a href="{{ route('register') }}" class="flex items-center gap-2 px-6 py-3 bg-white/10 border border-white/20 text-white rounded-xl font-semibold text-sm hover:bg-white/20 transition-colors">
                    <span class="material-symbols-outlined text-base">app_registration</span>
                    Staff Registration
                </a>
                <a href="{{ route('patient.login') }}" class="flex items-center gap-2 px-6 py-3 bg-growth-sage text-white rounded-xl font-semibold text-sm hover:bg-[#5f8c69] transition-colors">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">family_restroom</span>
                    Patient Portal Login
                </a>
                <a href="{{ route('patient.register') }}" class="flex items-center gap-2 px-6 py-3 bg-white/10 border border-white/20 text-white rounded-xl font-semibold text-sm hover:bg-white/20 transition-colors">
                    <span class="material-symbols-outlined text-base">app_registration</span>
                    Patient Registration
                </a>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-trust-navy text-on-primary w-full border-t border-white/10">
    <div class="py-12 px-margin-page max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-growth-sage text-2xl" style="font-variation-settings: 'FILL' 1;">spa</span>
            <span class="font-headline-sm font-bold text-on-primary">GAMUT-C</span>
        </div>
        <p class="font-body-sm text-body-sm opacity-60 text-center">© 2026 GAMUT-C. All rights reserved. HIPAA Compliant.</p>
        <div class="flex gap-6">
            <a href="{{ route('features') }}" class="font-body-sm text-body-sm opacity-70 hover:opacity-100 hover:text-growth-sage transition-colors">Features</a>
            <a href="{{ route('demo') }}" class="font-body-sm text-body-sm opacity-70 hover:opacity-100 hover:text-growth-sage transition-colors">Demo</a>
            <a href="{{ route('how-it-works') }}" class="font-body-sm text-body-sm opacity-70 hover:opacity-100 hover:text-growth-sage transition-colors">How It Works</a>
        </div>
    </div>
</footer>

<script>
    // Scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.section-fade').forEach(el => observer.observe(el));

    // Header shadow on scroll
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            header.classList.add('shadow-md', 'bg-surface/95', 'backdrop-blur-md');
        } else {
            header.classList.remove('shadow-md', 'bg-surface/95', 'backdrop-blur-md');
        }
    });
</script>
</body>
</html>
