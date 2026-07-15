<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>GAMUT-C | Demo</title>
    
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
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('features') }}">Features</a>
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('home') }}#modules">Modules</a>
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('how-it-works') }}">How It Works</a>
            <a class="font-body-md text-body-md text-trust-navy font-semibold transition-colors" href="{{ route('demo') }}">Demo</a>
            
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
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-btn" class="text-trust-navy p-2 hover:bg-slate-50 rounded-md transition-colors">
                <span class="material-symbols-outlined text-3xl" id="menu-icon">menu</span>
            </button>
        </div>
    </nav>
    
    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu" class="hidden absolute top-20 left-0 w-full bg-white shadow-lg border-t border-slate-100 z-40 md:hidden">
        <div class="flex flex-col px-6 py-6 gap-5">
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('features') }}">Features</a>
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('home') }}#modules">Modules</a>
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('how-it-works') }}">How It Works</a>
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('demo') }}">Demo</a>
            
            <div class="h-px w-full bg-slate-100 my-2"></div>
            
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-trust-navy text-white text-center px-6 py-3 rounded-md font-label-md text-label-md uppercase tracking-wider transition-all hover:bg-slate-dark shadow-sm">
                        {{ __('Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">login</span> {{ __('Log in') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-trust-navy text-white text-center px-6 py-3 rounded-md font-label-md text-label-md uppercase tracking-wider transition-all hover:bg-slate-dark shadow-sm">
                            {{ __('Register') }}
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</header>
<main class="pt-32 pb-24 bg-surface min-h-screen">
    <div class="max-w-4xl mx-auto px-margin-page">
        <!-- Header -->
        <div class="mb-12">
            <h1 class="font-headline-xl text-headline-xl text-trust-navy mb-4">Fully Working GAMUT-C Demo</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant">
                We offer fully functional demo installations for you to try out. Some simple configuration has been added for clearer demonstration of GAMUT-C, medical billing, access controls and patient portal. Each demo is reset overnight so no data is persistent.
            </p>
        </div>

        <!-- System Demo -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm mb-12 overflow-hidden">
            <div class="p-8 border-b border-outline-variant/30 bg-trust-navy/5">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy">GAMUT-C Demo</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Access the main clinical and administrative system.</p>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <h3 class="font-headline-md text-headline-md text-trust-navy mb-4">Links</h3>
                    <div class="grid gap-4">
                        <a href="{{ route('login') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Staff Login (General)</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('doctor.login') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Doctor & Clinician Portal</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/doctor/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('hospital.login') }}" class="flex items-center justify-between p-4 rounded-xl border border-rose-200 hover:border-rose-400/50 hover:bg-rose-50/50 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Hospital Administration Portal</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/hospital/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-rose-600 group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('pharmacy.login') }}" class="flex items-center justify-between p-4 rounded-xl border border-indigo-200 hover:border-indigo-400/50 hover:bg-indigo-50/50 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Pharmacy Portal</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/pharmacy/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Staff Registration</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/register') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('kyc') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Identity Verification (KYC Onboarding)</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/kyc') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('subscribe') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Manage Subscription Plan</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/subscribe') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-headline-md text-headline-md text-trust-navy mb-4">Credentials</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b-2 border-outline-variant/50">
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy whitespace-nowrap">Username</th>
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy whitespace-nowrap">Password</th>
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy">Description</th>
                                </tr>
                            </thead>
                            <tbody class="font-body-md text-body-md">
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">admin@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Admin User (Super Admin)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">sarah@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Dr. Sarah Chen (Attending)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">james@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Dr. James Wilson (Attending)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">mike@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Dr. Mike Rivera (Resident)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">lisa@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Lisa Park (Clinical Staff)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">karen@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Karen Miller (Billing Admin)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">pharmacy@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">PharmD. Elena Ruiz (Pharmacist)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">test@example.com</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Test User</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Portal Demo -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-outline-variant/30 bg-growth-sage/10">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy">Patient Portal Demo</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Experience the system from the patient's perspective.</p>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <h3 class="font-headline-md text-headline-md text-trust-navy mb-4">Links</h3>
                    <div class="grid gap-4">
                        <a href="{{ route('patient.login') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-growth-sage/30 hover:bg-growth-sage/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Patient Portal Login</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/patient/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-growth-sage group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('patient.register') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-growth-sage/30 hover:bg-growth-sage/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Patient Portal Registration</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/patient/register') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-growth-sage group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-headline-md text-headline-md text-trust-navy mb-4">Patient Credentials</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b-2 border-outline-variant/50">
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy">Email</th>
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy">Password</th>
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy">Description</th>
                                </tr>
                            </thead>
                            <tbody class="font-body-md text-body-md">
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">guardian@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Parent / Guardian (use Patient Portal Login)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                if (mobileMenu.classList.contains('hidden')) {
                    menuIcon.textContent = 'menu';
                } else {
                    menuIcon.textContent = 'close';
                }
            });
        }
    });
</script>
</body>
</html>
