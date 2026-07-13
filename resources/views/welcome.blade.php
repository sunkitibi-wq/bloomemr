<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Bloom EMR | Precision Care for Every Milestone</title>
    
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
<!-- Top Navigation Bar -->
<header class="fixed top-0 w-full z-50 bg-surface shadow-sm transition-all duration-300 h-20 flex items-center">
    <nav class="flex justify-between items-center px-margin-page w-full max-w-7xl mx-auto">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-trust-navy text-3xl" style="font-variation-settings: 'FILL' 1;">spa</span>
            <span class="font-headline-md text-headline-md font-bold text-trust-navy">Bloom EMR</span>
        </div>
        <div class="hidden md:flex gap-8 items-center">
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="#features">Features</a>
            <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="#technology">Technology</a>
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
<main class="pt-20">
<!-- Hero Section -->
<section class="relative min-h-[870px] flex items-center overflow-hidden bg-surface-bright">
<div class="absolute inset-0 cellular-bg opacity-40"></div>
<div class="max-w-7xl mx-auto px-margin-page grid grid-cols-1 lg:grid-cols-2 gap-gutter items-center relative z-10 py-16">
<div class="space-y-8 animate-fade-in-up">
<div class="inline-flex items-center gap-2 px-3 py-1 bg-growth-sage/10 text-growth-sage rounded-full border border-growth-sage/20">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
<span class="font-label-md text-label-md uppercase tracking-wider">HIPAA Compliant Platform</span>
</div>
<h1 class="font-headline-xl text-headline-xl text-trust-navy leading-tight">
                        Precision Care for Every <span class="text-growth-sage">Milestone.</span>
</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
                        The only EMR built specifically for child psychiatry and neurodevelopmental medicine. Streamline ADOS-2 workflows and developmental tracking in one unified interface.
                    </p>
<div class="flex flex-col sm:flex-row gap-4">
@auth
    <a href="{{ route('dashboard') }}" class="bg-trust-navy text-on-primary px-8 py-4 rounded-xl font-headline-md text-body-md transition-all hover:shadow-lg active:scale-[0.98] flex items-center justify-center gap-2">
        {{ __('Go to Dashboard') }}
        <span class="material-symbols-outlined">arrow_forward</span>
    </a>
@else
    <a href="{{ route('login') }}" class="bg-trust-navy text-on-primary px-8 py-4 rounded-xl font-headline-md text-body-md transition-all hover:shadow-lg active:scale-[0.98] flex items-center justify-center gap-2">
        {{ __('Get Started') }}
        <span class="material-symbols-outlined">arrow_forward</span>
    </a>
@endauth
<a href="#features" class="border border-outline-variant text-trust-navy px-8 py-4 rounded-xl font-headline-md text-body-md transition-all hover:bg-surface-container-low active:scale-[0.98] text-center flex items-center justify-center">
                            Explore Features
</a>
</div>
<div class="flex items-center gap-6 pt-4">
<div class="flex -space-x-3">
<img class="w-10 h-10 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDmR5r4YCo3RG-cygjjpLWJfeIlX6Fvmc-VtIk63Xpq4WHQChL6ku_zOl7a8TISFTY__uhtd2-5JhUo8kc2zaSWwUaeyG4bLVOIXdAvQSGaESxYZw5073yZ-ir2djFPhuUvx--4xq0BRZNjD1SJ_Y6XaEBASc8uvdYyZJyMImiqZdgSIjerAPnkJ35XsOhZglEG_wfAlOxKFm30MhGrjyLCtd7Hky9nwJ6px6JL5fYKRwLhavBppPFGWBuet31DVwLx_mv5tHgn4NQ"/>
<img class="w-10 h-10 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCDzrM0GhmpJ64gDajfinSdgeIIKU67NnZUOGFuQuJyeMupVTBhQ0rGi7Jco5v9fPfRmRQA7rV9ZEh1AHibh7tphKx4dzoiygMH6WO22k3yzszW06VfqcZZiFYPMPu24xGitQjaxEcGRzYaWLiQLfsed-vhe27WdAVjp0s7cq9-TOyVv3i2DFaqvQRJTKAWu9KuQ3S3csdkPp-hgSpjh9HykKJFoSG_heRh7TnckVBTfypwIGopB6GTm_V0cLHsvlbTRgpyiplW-OA"/>
<img class="w-10 h-10 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCxOVaq7Js00iMZxqtHTMdjIiq7q10nS8vgkDLGAkvKEPdc7rRSGlHWPqGSvKhHFE_DqGDpj5iCg9eYFNAfKdCn_2L-OcQrdJGyXifVqooJzZ30j3kOVkGL0jjXg8nO_VSdp_hwiX0XoKaDyZUlrjJtxYlw4SzDyc0DWZzaV47Bik-i-nYfmIazUhl65c1mxJM01KEMxTJVUC_2UxtrBxZDRUbilPNASmtUgRFpLW6DkTJbj1G5z-KidRjQzuVKA680pv77GYoGSLA"/>
</div>
<p class="font-label-md text-label-md text-on-surface-variant">Trusted by 200+ specialized practices</p>
</div>
</div>
<div class="relative lg:h-[600px] flex justify-center lg:justify-end">
<div class="w-full h-full max-w-lg relative bg-surface-container-lowest rounded-2xl overflow-hidden shadow-2xl border border-outline-variant/30">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4RSNk6C2tvX3N1IOca6D58G5gJqAJVEktyxiE_X2U4_GAl6yHDiyBgHRbATXJidj51Wft2oJH0HdltGNEjqesKUQexxAhBk5uj873TGSrgBoCbNRfIRErhCX6rAgjGMWxo-xuamUHcjLxvjwAV7FD8HYvRwwic1NsXiJqsRRiiZ-s6cakIkfWVD9q3i2QogHJn7TGzzl39Q8-MtjZ8i_A3GL3u_9ZtHdwJVlH6UYuQhiVWEApSLlnsxeJ7XcMGNM3U6CuyEd0Q-A"/>
<div class="absolute bottom-6 left-6 right-6 glass-card p-4 rounded-xl shadow-xl flex items-center gap-4 animate-bounce-subtle">
<div class="w-12 h-12 bg-growth-sage/20 rounded-lg flex items-center justify-center text-growth-sage">
<span class="material-symbols-outlined">analytics</span>
</div>
<div>
<p class="font-label-md text-label-md text-trust-navy">ADOS-2 Processing</p>
<p class="font-data-mono text-data-mono text-on-surface-variant">Real-time score calculation...</p>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Stats Section -->
<section class="py-12 bg-trust-navy text-on-primary">
<div class="max-w-7xl mx-auto px-margin-page grid grid-cols-1 md:grid-cols-3 gap-gutter text-center">
<div class="p-6">
<h3 class="font-headline-xl text-headline-xl text-growth-sage">40%</h3>
<p class="font-body-md text-body-md opacity-80">Reduction in documentation time</p>
</div>
<div class="p-6 border-y md:border-y-0 md:border-x border-on-primary/10">
<h3 class="font-headline-xl text-headline-xl text-growth-sage">99.9%</h3>
<p class="font-body-md text-body-md opacity-80">Uptime for critical clinical ops</p>
</div>
<div class="p-6">
<h3 class="font-headline-xl text-headline-xl text-growth-sage">100%</h3>
<p class="font-body-md text-body-md opacity-80">HIPAA audit coverage success</p>
</div>
</div>
</section>
<!-- Feature Grid Section -->
<section class="py-24 bg-surface" id="features">
<div class="max-w-7xl mx-auto px-margin-page">
<div class="text-center mb-16 space-y-4">
<h2 class="font-headline-lg text-headline-lg text-trust-navy">Engineered for Specialized Care</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">Powerful tools designed to match the complexity of neurodevelopmental assessments.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
<!-- Feature 1 -->
<div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/30 hover:shadow-xl transition-all group">
<div class="w-14 h-14 bg-trust-navy text-on-primary rounded-xl flex items-center justify-center mb-6 group-hover:bg-growth-sage transition-colors">
<span class="material-symbols-outlined text-2xl">clinical_notes</span>
</div>
<h3 class="font-headline-md text-headline-md text-trust-navy mb-3">Clinical Precision</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6">Built-in templates for ADOS-2, Vanderbilt, and M-CHAT. Structured data capture ensures consistent longitudinal tracking of developmental milestones.</p>
<ul class="space-y-3 font-label-md text-label-md text-trust-navy">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Automated Scoring Engines</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Longitudinal Growth Charts</li>
</ul>
</div>
<!-- Feature 2 -->
<div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/30 hover:shadow-xl transition-all group">
<div class="w-14 h-14 bg-trust-navy text-on-primary rounded-xl flex items-center justify-center mb-6 group-hover:bg-growth-sage transition-colors">
<span class="material-symbols-outlined text-2xl">auto_fix_high</span>
</div>
<h3 class="font-headline-md text-headline-md text-trust-navy mb-3">AI-Powered Productivity</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6">Intelligent Smart Phrases and auto-population tools that learn your clinical style. Spend more time with patients, less with the keyboard.</p>
<ul class="space-y-3 font-label-md text-label-md text-trust-navy">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Context-Aware Dot Phrases</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Instant Report Generation</li>
</ul>
</div>
<!-- Feature 3 -->
<div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/30 hover:shadow-xl transition-all group">
<div class="w-14 h-14 bg-trust-navy text-on-primary rounded-xl flex items-center justify-center mb-6 group-hover:bg-growth-sage transition-colors">
<span class="material-symbols-outlined text-2xl">family_restroom</span>
</div>
<h3 class="font-headline-md text-headline-md text-trust-navy mb-3">Patient-Centered Portal</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6">Family-first secure messaging and document sharing. Keep caregivers informed with a simplified portal optimized for mobile access.</p>
<ul class="space-y-3 font-label-md text-label-md text-trust-navy">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Secure Caregiver Inbox</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-growth-sage text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span> Mobile-Ready Bill Pay</li>
</ul>
</div>
</div>
</div>
</section>
<!-- Technology Section -->
<section class="py-24 bg-surface-container-lowest overflow-hidden" id="technology">
<div class="max-w-7xl mx-auto px-margin-page">
<div class="flex flex-col lg:flex-row gap-gutter items-center">
<div class="flex-1 space-y-6">
<div class="inline-block px-3 py-1 bg-trust-navy/5 text-trust-navy font-label-md text-label-md rounded-lg">Architecture & Security</div>
<h2 class="font-headline-lg text-headline-lg text-trust-navy">Modern Stack, Uncompromising Security</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">Built on the robust Laravel v13 ecosystem, Bloom offers bank-grade encryption and seamless interoperability. Our infrastructure is designed for high-availability clinical environments.</p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
<div class="p-4 bg-surface-container rounded-xl flex items-start gap-4">
<span class="material-symbols-outlined text-trust-navy mt-1">shield_lock</span>
<div>
<h4 class="font-headline-md text-body-md font-bold text-trust-navy">HIPAA Certified</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Full compliance with medical privacy regulations.</p>
</div>
</div>
<div class="p-4 bg-surface-container rounded-xl flex items-start gap-4">
<span class="material-symbols-outlined text-trust-navy mt-1">prescriptions</span>
<div>
<h4 class="font-headline-md text-body-md font-bold text-trust-navy">Seamless e-Prescribing</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Integrated Surescripts PDMP checking.</p>
</div>
</div>
</div>
</div>
<div class="flex-1 relative">
<div class="absolute inset-0 bg-growth-sage/10 rounded-full blur-3xl -z-10"></div>
<div class="bg-trust-navy p-6 rounded-2xl shadow-2xl border border-on-primary/10 w-full max-w-md mx-auto">
<div class="flex items-center justify-between mb-8">
<div class="flex items-center gap-2">
<div class="w-3 h-3 rounded-full bg-status-critical"></div>
<div class="w-3 h-3 rounded-full bg-status-warning"></div>
<div class="w-3 h-3 rounded-full bg-growth-sage"></div>
</div>
<span class="font-data-mono text-xs text-on-primary/40 uppercase">v13.4.2-stable</span>
</div>
<div class="space-y-4 font-data-mono text-sm">
<p class="text-growth-sage">system_init: <span class="text-on-primary">securing_data_nodes...</span></p>
<p class="text-growth-sage">auth_service: <span class="text-on-primary">aes_256_active</span></p>
<p class="text-growth-sage">db_query: <span class="text-on-primary">fetching_patient_milestones</span></p>
<div class="h-[2px] bg-on-primary/10 w-full"></div>
<p class="text-on-primary/60">Executing SmartPhrase interpolation...</p>
<p class="text-status-warning">Monitoring latency: 24ms</p>
<div class="p-3 bg-on-primary/5 rounded border border-on-primary/10">
<p class="text-on-primary">Patient: Case #NF-9281</p>
<p class="text-on-primary/60">Risk Score: Low</p>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Final CTA Section -->
<section class="py-24 px-margin-page relative overflow-hidden">
<div class="absolute inset-0 bg-growth-sage/5 -z-10"></div>
<div class="max-w-4xl mx-auto text-center space-y-10">
<h2 class="font-headline-xl text-headline-xl text-trust-navy">Ready to modernize your practice?</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">Join the future of neurodevelopmental care with the most intuitive, specialized clinical toolset on the market.</p>
<div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
@auth
    <a href="{{ route('dashboard') }}" class="bg-trust-navy text-on-primary px-10 py-5 rounded-2xl font-headline-md text-headline-md transition-all hover:scale-[1.02] shadow-xl hover:shadow-trust-navy/20">
        {{ __('Go to Dashboard') }}
    </a>
@else
    <a href="{{ route('login') }}" class="bg-trust-navy text-on-primary px-10 py-5 rounded-2xl font-headline-md text-headline-md transition-all hover:scale-[1.02] shadow-xl hover:shadow-trust-navy/20">
        {{ __('Get Started Today') }}
    </a>
@endauth
</div>
</div>
</section>
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
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#features">Features</a>
<a class="font-body-sm text-body-sm opacity-80 hover:text-growth-sage transition-colors" href="#technology">Technology</a>
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
                    © 2026 Bloom EMR. All rights reserved. HIPAA Compliant.
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
