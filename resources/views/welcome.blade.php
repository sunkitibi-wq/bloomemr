@extends('layouts.frontend')

@section('title', 'GAMUT-C | Precision Care for Every Milestone')

@section('content')

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
<div class="relative h-[350px] sm:h-[400px] lg:h-[500px] flex justify-center lg:justify-end mt-12 lg:mt-0 w-full">
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
<!-- Pricing Section -->
<section class="py-24 bg-surface-container-lowest" id="pricing">
    <div class="max-w-7xl mx-auto px-margin-page">
        <div class="text-center mb-16 space-y-4">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-growth-sage/10 text-growth-sage rounded-full text-xs font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm">payments</span>
                Pricing Options
            </div>
            <h2 class="font-headline-lg text-4xl text-trust-navy font-bold">Simple, Transparent Licensing</h2>
            <p class="font-body-lg text-body-lg text-slate-dark max-w-2xl mx-auto">Choose the license that fits your practice model. All plans include full clinical features.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-4xl mx-auto">
            <!-- Individual Practitioner Card -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <span class="text-xs font-bold text-growth-sage uppercase tracking-wider">{{ __('Individual License') }}</span>
                    <h3 class="text-2xl font-bold text-trust-navy mt-2">{{ __('Practitioner Plan') }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ __('Billed per clinical user. Choose your duration.') }}</p>
                    
                    <div class="my-8 space-y-4">
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-extrabold text-trust-navy">$99</span>
                            <span class="text-sm text-slate-500">/ {{ __('Month') }}</span>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl flex justify-between items-center text-xs">
                            <span class="font-semibold text-slate-700">{{ __('6 Month Save Plan') }}</span>
                            <span class="font-bold text-trust-navy">$499 <span class="text-growth-sage text-[10px] uppercase font-extrabold ml-1">Save 15%</span></span>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl flex justify-between items-center text-xs">
                            <span class="font-semibold text-slate-700">{{ __('1 Year Value Plan') }}</span>
                            <span class="font-bold text-trust-navy">$899 <span class="text-primary text-[10px] uppercase font-extrabold ml-1">Save 25%</span></span>
                        </div>
                    </div>

                    <ul class="space-y-3.5 text-xs text-slate-600 border-t border-slate-100 pt-6">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span>Full SOAP/DAP/Intake Clinical Notes</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span>Electronic Prescribing & Refill Audits</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span>Patient Portal Access for Guardians</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span>Billing Cycle & Claims Manager Access</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('register') }}" class="block w-full py-3 bg-growth-sage text-white text-center rounded-xl font-bold hover:bg-growth-sage/95 shadow-sm text-sm">
                        {{ __('Register & Subscribe') }}
                    </a>
                </div>
            </div>

            <!-- Enterprise Card -->
            <div class="bg-gradient-to-br from-trust-navy to-slate-900 p-8 rounded-2xl shadow-sm text-white flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <span class="text-xs font-bold text-growth-sage uppercase tracking-wider">{{ __('Organization Plan') }}</span>
                    <h3 class="text-2xl font-bold text-white mt-2">{{ __('Clinic & Health System') }}</h3>
                    <p class="text-xs text-slate-350 mt-1">{{ __('Multi-practitioner practices and medical centers.') }}</p>
                    
                    <div class="my-8">
                        <div class="text-3xl font-extrabold text-white">{{ __('Custom Licensing') }}</div>
                        <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                            {{ __('Organization licenses must be configured directly by our support team. Includes volume discounts for multi-user practices.') }}
                        </p>
                    </div>

                    <ul class="space-y-3.5 text-xs text-slate-200 border-t border-white/10 pt-6">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span>Dedicated Private HIPAA Server Instance</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span>Custom HIE & Lab Integration Endpoints</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span>Volume Discounts & Clinic Roster Control</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span>SLA & Technical Support Manager</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-white/10">
                    <a href="{{ route('how-it-works') }}#pricing" class="block w-full py-3 bg-white/10 hover:bg-white/20 border border-white/15 text-white text-center rounded-xl font-bold text-sm transition-colors">
                        {{ __('View Organization Guide') }}
                    </a>
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
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
@endsection
