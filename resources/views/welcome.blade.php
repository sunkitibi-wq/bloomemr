<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>GAMUT-C | Precision Care for Every Milestone</title>
    
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
        .cellular-bg {
            background-image: radial-gradient(circle, #0F172A08 1px, transparent 1px);
            background-size: 32px 32px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-growth-sage/30">
<!-- Top Utility Bar -->
<div class="bg-surface-container-low border-b border-outline-variant/30 py-2 hidden md:block">
    <div class="max-w-7xl mx-auto px-margin-page flex justify-end gap-6">
        <a href="#" class="font-body-sm text-body-sm text-on-surface-variant hover:text-trust-navy transition-colors">Logins & support</a>
        <a href="#" class="font-body-sm text-body-sm text-on-surface-variant hover:text-trust-navy transition-colors">Prior Authorization Portal</a>
    </div>
</div>
<!-- Main Navigation Bar -->
<header class="sticky top-0 w-full z-50 bg-white shadow-sm transition-all duration-300 h-20 flex items-center">
    <nav class="flex justify-between items-center px-margin-page w-full max-w-7xl mx-auto">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-trust-navy text-3xl" style="font-variation-settings: 'FILL' 1;">spa</span>
            <span class="font-headline-md text-headline-md font-bold text-trust-navy uppercase tracking-wide">GAMUT-C</span>
        </div>
        <div class="hidden md:flex gap-10 items-center">
            <a class="font-headline-sm text-body-md text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('features') }}">Who we serve</a>
            <a class="font-headline-sm text-body-md text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="#modules">Products</a>
            <a class="font-headline-sm text-body-md text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('demo') }}">Network</a>
            
            <div class="flex items-center gap-4 ml-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-trust-navy text-white px-6 py-2 rounded-md font-label-md text-label-md uppercase tracking-wider transition-all hover:bg-slate-dark shadow-sm">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a class="font-headline-sm text-body-md text-trust-navy font-semibold hover:text-growth-sage transition-colors flex items-center gap-1" href="{{ route('login') }}">
                            <span class="material-symbols-outlined text-lg">login</span> {{ __('Log in') }}
                        </a>
                        @if (Route::has('register'))
                            <a class="bg-trust-navy text-white px-6 py-2 rounded-md font-label-md text-label-md uppercase tracking-wider transition-all hover:bg-slate-dark shadow-sm ml-2" href="{{ route('register') }}">
                                {{ __('Register') }}
                            </a>
                        @endif
                    @endauth
                @endif
                <button class="text-trust-navy hover:text-growth-sage transition-colors ml-2">
                    <span class="material-symbols-outlined text-2xl">search</span>
                </button>
            </div>
        </div>
        <div class="md:hidden">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-trust-navy uppercase">Dashboard</a>
                @else
                    <div class="flex flex-col gap-2 mt-4">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-trust-navy uppercase">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-semibold text-trust-navy uppercase">Register</a>
                        @endif
                    </div>
                @endauth
            @endif
        </div>
    </nav>
</header>
<main>
<!-- Hero Section -->
<section class="relative min-h-[700px] flex items-center overflow-hidden bg-trust-navy text-white">
<div class="max-w-7xl mx-auto px-margin-page grid grid-cols-1 lg:grid-cols-2 gap-gutter items-center relative z-10 py-20">
<div class="space-y-8 animate-fade-in-up pr-8">
<h1 class="font-headline-xl text-headline-xl text-white leading-tight font-bold">
                        The Healthcare Network.
</h1>
<p class="font-body-lg text-body-lg text-white/90 max-w-xl text-lg">
                        We do not replace a hospital management system. Instead, we connect Hospitals, Clinics, Pharmacies, Insurance companies, PBMs, and Laboratories.
                    </p>
<div class="flex flex-col sm:flex-row gap-4 pt-4">
@auth
    <a href="{{ route('dashboard') }}" class="bg-growth-sage text-white px-8 py-4 rounded-md font-headline-md text-body-md font-semibold transition-all hover:shadow-lg hover:bg-opacity-90 flex items-center justify-center gap-2">
        {{ __('Go to Dashboard') }}
    </a>
@else
    <a href="{{ route('register') }}" class="bg-growth-sage text-white px-8 py-4 rounded-md font-headline-md text-body-md font-semibold transition-all hover:shadow-lg hover:bg-opacity-90 flex items-center justify-center gap-2">
        {{ __('Get Started') }}
    </a>
@endauth
<a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-md font-headline-md text-body-md font-semibold transition-all hover:bg-white hover:text-trust-navy text-center flex items-center justify-center">
                            Learn more
</a>
</div>
</div>
<div class="relative lg:h-[500px] flex justify-center lg:justify-end mt-12 lg:mt-0">
<div class="w-full h-full max-w-lg relative bg-slate-dark rounded-xl overflow-hidden shadow-2xl border-4 border-slate-dark/50 shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
<img class="w-full h-full object-cover mix-blend-overlay opacity-80" src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Medical professional using tablet"/>
<div class="absolute inset-0 bg-gradient-to-t from-trust-navy/90 to-transparent flex flex-col justify-end p-8">
    <h3 class="text-2xl font-bold text-white mb-2">Empowering Care Decisions</h3>
    <p class="text-white/80">Connecting practices with critical patient insights at the point of care.</p>
</div>
</div>
</div>
</div>
</section>
<!-- Stats Section -->
<section class="py-16 bg-slate-dark text-white">
<div class="max-w-7xl mx-auto px-margin-page grid grid-cols-1 md:grid-cols-3 gap-12 text-center divide-y md:divide-y-0 md:divide-x divide-white/20">
<div class="pt-8 md:pt-0">
<h3 class="font-headline-xl text-3xl text-growth-sage font-bold mb-2">Patient Safety</h3>
<p class="font-body-md text-body-md text-white/80">Reduce prescription errors, duplicate tests, and medication conflicts.</p>
</div>
<div class="pt-8 md:pt-0">
<h3 class="font-headline-xl text-3xl text-growth-sage font-bold mb-2">HIPAA Compliant</h3>
<p class="font-body-md text-body-md text-white/80">User authentication, role-based access, audit logs, and secure encryption.</p>
</div>
<div class="pt-8 md:pt-0">
<h3 class="font-headline-xl text-3xl text-growth-sage font-bold mb-2">Clinical Workflows</h3>
<p class="font-body-md text-body-md text-white/80">Streamline patient registration, clinical documentation, and care coordination.</p>
</div>
</div>
</section>
<!-- Feature Grid Section -->
<section class="py-24 bg-white" id="features">
<div class="max-w-7xl mx-auto px-margin-page">
<div class="text-center mb-16 space-y-4">
<h2 class="font-headline-lg text-4xl text-trust-navy font-bold">Core Capabilities</h2>
<p class="font-body-lg text-body-lg text-slate-dark max-w-2xl mx-auto">Providing the foundational tools required for modern healthcare delivery and communication.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-10">
<!-- Feature 1 -->
<div class="bg-white p-8 hover:-translate-y-1 transition-transform group shadow-sm border border-slate-dark/5 rounded-xl">
<div class="w-16 h-16 bg-white border-2 border-growth-sage text-trust-navy rounded-full flex items-center justify-center mb-6 group-hover:bg-growth-sage group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-3xl">folder_shared</span>
</div>
<h3 class="font-headline-md text-2xl text-trust-navy mb-4 font-bold">Electronic Health Records</h3>
<p class="font-body-md text-body-md text-slate-dark mb-6 leading-relaxed">Store patient demographics, access medical history, view medications, review allergies, view laboratory results, and track diagnoses seamlessly.</p>
<ul class="space-y-3 font-label-md text-label-md text-trust-navy">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Comprehensive Patient Data</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Direct Data Management</li>
</ul>
</div>
<!-- Feature 2 -->
<div class="bg-white p-8 hover:-translate-y-1 transition-transform group shadow-sm border border-slate-dark/5 rounded-xl">
<div class="w-16 h-16 bg-white border-2 border-growth-sage text-trust-navy rounded-full flex items-center justify-center mb-6 group-hover:bg-growth-sage group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-3xl">prescriptions</span>
</div>
<h3 class="font-headline-md text-2xl text-trust-navy mb-4 font-bold">Electronic Prescriptions</h3>
<p class="font-body-md text-body-md text-slate-dark mb-6 leading-relaxed">Prescribe medications electronically, check drug interactions, verify medication history, and send prescriptions directly to pharmacies.</p>
<ul class="space-y-3 font-label-md text-label-md text-trust-navy">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Interaction Checking</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Direct Pharmacy Routing</li>
</ul>
</div>
<!-- Feature 3 -->
<div class="bg-white p-8 hover:-translate-y-1 transition-transform group shadow-sm border border-slate-dark/5 rounded-xl">
<div class="w-16 h-16 bg-white border-2 border-growth-sage text-trust-navy rounded-full flex items-center justify-center mb-6 group-hover:bg-growth-sage group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-3xl">hub</span>
</div>
<h3 class="font-headline-md text-2xl text-trust-navy mb-4 font-bold">Healthcare Interoperability</h3>
<p class="font-body-md text-body-md text-slate-dark mb-6 leading-relaxed">Exchange healthcare data between systems using common standards, enabling hospitals, clinics, and labs to communicate electronically.</p>
<ul class="space-y-3 font-label-md text-label-md text-trust-navy">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> HL7, FHIR, CCD/C-CDA</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> ICD-10, SNOMED CT, LOINC</li>
</ul>
</div>
</div>
</div>
</section>
<!-- Modules Section -->
<section class="py-24 bg-surface-container-low" id="modules">
<div class="max-w-7xl mx-auto px-margin-page">
<div class="text-center mb-16 space-y-4">
<h2 class="font-headline-lg text-4xl text-trust-navy font-bold">Connecting the Healthcare Ecosystem</h2>
<p class="font-body-lg text-body-lg text-slate-dark max-w-2xl mx-auto">We connect key entities across the continuum of care to specialize in e-Prescribing, medication history, prior authorization, and health information exchange.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<!-- Module 1 -->
<div class="bg-white p-8 shadow-sm border-t-4 border-transparent hover:border-growth-sage hover:shadow-md transition-all flex flex-col gap-4 group rounded-xl">
<div class="w-12 h-12 text-growth-sage flex items-center shrink-0">
<span class="material-symbols-outlined text-4xl">local_hospital</span>
</div>
<div>
<h3 class="font-headline-md text-xl text-trust-navy font-bold mb-2">Hospitals</h3>
<p class="font-body-sm text-body-sm text-slate-dark">Integrated networks for inpatient care and system-wide intelligence.</p>
</div>
</div>

<!-- Module 2 -->
<div class="bg-white p-8 shadow-sm border-t-4 border-transparent hover:border-growth-sage hover:shadow-md transition-all flex flex-col gap-4 group rounded-xl">
<div class="w-12 h-12 text-growth-sage flex items-center shrink-0">
<span class="material-symbols-outlined text-4xl">medical_services</span>
</div>
<div>
<h3 class="font-headline-md text-xl text-trust-navy font-bold mb-2">Clinics</h3>
<p class="font-body-sm text-body-sm text-slate-dark">Point-of-care connectivity for outpatient practitioners and specialists.</p>
</div>
</div>

<!-- Module 3 -->
<div class="bg-white p-8 shadow-sm border-t-4 border-transparent hover:border-growth-sage hover:shadow-md transition-all flex flex-col gap-4 group rounded-xl">
<div class="w-12 h-12 text-growth-sage flex items-center shrink-0">
<span class="material-symbols-outlined text-4xl">local_pharmacy</span>
</div>
<div>
<h3 class="font-headline-md text-xl text-trust-navy font-bold mb-2">Pharmacies</h3>
<p class="font-body-sm text-body-sm text-slate-dark">Direct electronic prescribing and medication fulfillment workflows.</p>
</div>
</div>

<!-- Module 4 -->
<div class="bg-white p-8 shadow-sm border-t-4 border-transparent hover:border-growth-sage hover:shadow-md transition-all flex flex-col gap-4 group rounded-xl">
<div class="w-12 h-12 text-growth-sage flex items-center shrink-0">
<span class="material-symbols-outlined text-4xl">health_and_safety</span>
</div>
<div>
<h3 class="font-headline-md text-xl text-trust-navy font-bold mb-2">Insurance Companies</h3>
<p class="font-body-sm text-body-sm text-slate-dark">Streamlined prior authorizations and eligibility verifications.</p>
</div>
</div>

<!-- Module 5 -->
<div class="bg-white p-8 shadow-sm border-t-4 border-transparent hover:border-growth-sage hover:shadow-md transition-all flex flex-col gap-4 group rounded-xl">
<div class="w-12 h-12 text-growth-sage flex items-center shrink-0">
<span class="material-symbols-outlined text-4xl">verified_user</span>
</div>
<div>
<h3 class="font-headline-md text-xl text-trust-navy font-bold mb-2">PBMs</h3>
<p class="font-body-sm text-body-sm text-slate-dark">Pharmacy Benefit Managers ensuring cost-effective medication access.</p>
</div>
</div>

<!-- Module 6 -->
<div class="bg-white p-8 shadow-sm border-t-4 border-transparent hover:border-growth-sage hover:shadow-md transition-all flex flex-col gap-4 group rounded-xl">
<div class="w-12 h-12 text-growth-sage flex items-center shrink-0">
<span class="material-symbols-outlined text-4xl">science</span>
</div>
<div>
<h3 class="font-headline-md text-xl text-trust-navy font-bold mb-2">Laboratories</h3>
<p class="font-body-sm text-body-sm text-slate-dark">Seamless exchange of diagnostic reports and laboratory orders.</p>
</div>
</div>
</div>
</div>
</section>
<!-- Final CTA Section -->
<section class="py-24 px-margin-page bg-trust-navy text-white text-center">
<div class="max-w-4xl mx-auto space-y-10">
<h2 class="font-headline-xl text-4xl font-bold">Ready to modernize your practice?</h2>
<p class="font-body-lg text-xl text-white/90">Join the future of neurodevelopmental care with the most intuitive, specialized clinical toolset on the market.</p>
<div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
@auth
    <a href="{{ route('dashboard') }}" class="bg-growth-sage text-white px-10 py-4 rounded-md font-headline-md font-bold transition-all hover:bg-opacity-90">
        {{ __('Go to Dashboard') }}
    </a>
@else
    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="bg-growth-sage text-white px-10 py-4 rounded-md font-headline-md font-bold transition-all hover:bg-opacity-90">
        {{ __('Get Started Today') }}
    </a>
@endauth
<a href="#" class="text-white underline hover:text-growth-sage font-headline-md">Contact Sales</a>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-slate-dark text-white w-full border-t-[10px] border-growth-sage">
<div class="py-16 px-margin-page grid grid-cols-1 md:grid-cols-4 gap-12 max-w-7xl mx-auto">
<div class="md:col-span-1 space-y-6">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-growth-sage text-3xl" style="font-variation-settings: 'FILL' 1;">spa</span>
<span class="font-headline-sm text-2xl font-bold text-white tracking-wide uppercase">Bloom</span>
</div>
<p class="font-body-sm text-white/80 leading-relaxed pr-4">
                    Designed for practitioners, by practitioners. Leading the evolution of pediatric EMR systems.
                </p>
</div>
<div class="space-y-6">
<h4 class="font-headline-sm text-lg font-bold text-white border-b border-white/20 pb-2">Product</h4>
<nav class="flex flex-col gap-3">
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="{{ route('features') }}">Features</a>
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="#modules">Modules</a>
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="{{ route('demo') }}">Demo</a>
</nav>
</div>
<div class="space-y-6">
<h4 class="font-headline-sm text-lg font-bold text-white border-b border-white/20 pb-2">Compliance</h4>
<nav class="flex flex-col gap-3">
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="#">HIPAA Standards</a>
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="#">Privacy Policy</a>
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="#">Security Audit</a>
</nav>
</div>
<div class="space-y-6">
<h4 class="font-headline-sm text-lg font-bold text-white border-b border-white/20 pb-2">Resources</h4>
<nav class="flex flex-col gap-3">
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="#">Help Center</a>
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="#">Clinical Guides</a>
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="#">System Status</a>
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="#">Contact Support</a>
</nav>
</div>
</div>
<div class="border-t border-white/10 py-8 px-margin-page bg-[#2c3a47]">
<div class="max-w-7xl mx-auto flex justify-center text-center">
<p class="font-body-sm text-white/60">
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

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                    entry.target.classList.remove('opacity-0', 'translate-y-10');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-fade-in-up').forEach(el => {
            el.classList.add('transition-all', 'duration-700', 'opacity-0', 'translate-y-10');
            observer.observe(el);
        });
    });
</script>
</body>
</html>
