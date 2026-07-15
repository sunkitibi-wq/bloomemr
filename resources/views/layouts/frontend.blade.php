<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'GAMUT-C | Precision Care for Every Milestone')</title>
    
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
    @yield('head')
</head>
<body class="bg-background text-on-background font-body-md selection:bg-growth-sage/30">
<!-- Top Utility Bar -->
<div class="bg-surface-container-low border-b border-outline-variant/30 py-2 hidden md:block">
    <div class="max-w-7xl mx-auto px-margin-page flex justify-end gap-6">
        <a href="/login" class="font-body-sm text-body-sm text-on-surface-variant hover:text-trust-navy transition-colors">Logins & support</a>
        <a href="/register" class="font-body-sm text-body-sm text-on-surface-variant hover:text-trust-navy transition-colors">Prior Authorization Portal</a>
    </div>
</div>
<!-- Main Navigation Bar -->
<header class="sticky top-0 w-full z-50 bg-white shadow-sm transition-all duration-300 h-20 flex items-center">
    <nav class="flex justify-between items-center px-margin-page w-full max-w-7xl mx-auto">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="material-symbols-outlined text-trust-navy text-3xl" style="font-variation-settings: 'FILL' 1;">spa</span>
            <span class="font-headline-md text-headline-md font-bold text-trust-navy uppercase tracking-wide">GAMUT-C</span>
        </a>
        <div class="hidden md:flex gap-10 items-center">
            <a class="font-headline-sm text-body-md text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('features') }}">Who we serve</a>
            <a class="font-headline-sm text-body-md text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ url('/#modules') }}">Products</a>
            <a class="font-headline-sm text-body-md text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('how-it-works') }}">How It Works</a>
            <a class="font-headline-sm text-body-md text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ url('/#pricing') }}">Pricing</a>
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
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-btn" class="text-trust-navy p-2 hover:bg-slate-50 rounded-md transition-colors">
                <span class="material-symbols-outlined text-3xl" id="menu-icon">menu</span>
            </button>
        </div>
    </nav>
    
    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu" class="hidden absolute top-20 left-0 w-full bg-white shadow-lg border-t border-slate-100 z-40 md:hidden">
        <div class="flex flex-col px-6 py-6 gap-5">
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('features') }}">Who we serve</a>
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ url('/#modules') }}">Products</a>
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('how-it-works') }}">How It Works</a>
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ url('/#pricing') }}">Pricing</a>
            <a class="font-headline-sm text-body-lg text-trust-navy font-semibold hover:text-growth-sage transition-colors" href="{{ route('demo') }}">Network</a>
            
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
<main>
    @yield('content')
</main>
<!-- Footer -->
<footer class="bg-slate-dark text-white w-full border-t-[10px] border-growth-sage">
<div class="py-16 px-margin-page grid grid-cols-1 md:grid-cols-4 gap-12 max-w-7xl mx-auto">
<div class="md:col-span-1 space-y-6">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-growth-sage text-3xl" style="font-variation-settings: 'FILL' 1;">spa</span>
<span class="font-headline-sm text-2xl font-bold text-white tracking-wide uppercase">GAMUT-C</span>
</div>
<p class="font-body-sm text-white/80 leading-relaxed pr-4">
                    Designed for practitioners, by practitioners. Leading the evolution of pediatric EMR systems.
                </p>
</div>
<div class="space-y-6">
<h4 class="font-headline-sm text-lg font-bold text-white border-b border-white/20 pb-2">Product</h4>
<nav class="flex flex-col gap-3">
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="{{ route('features') }}">Features</a>
<a class="font-body-sm text-white/80 hover:text-growth-sage transition-colors" href="{{ url('/#modules') }}">Modules</a>
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
@yield('scripts')
</body>
</html>
