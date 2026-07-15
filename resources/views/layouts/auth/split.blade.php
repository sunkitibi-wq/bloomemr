<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
        
        <style>
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
            .bg-mesh {
                background: radial-gradient(at 0% 0%, rgba(116, 165, 127, 0.05) 0px, transparent 50%),
                            radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.03) 0px, transparent 50%);
            }
        </style>
    </head>
    <body class="bg-slate-50 dark:bg-zinc-950 font-sans antialiased text-zinc-900 dark:text-zinc-100">
        <main class="min-h-screen flex flex-col md:flex-row">
            <!-- Brand / Form Side -->
            <div class="flex-grow flex flex-col bg-mesh relative">
                <!-- Top Bar -->
                <header class="fixed top-0 w-full md:w-1/2 flex justify-between items-center px-8 h-16 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md z-10 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="font-headline-md text-lg font-bold text-trust-navy dark:text-zinc-100">
                        GAMUT-C<span class="text-growth-sage"> EMR</span>
                    </div>
                    <div class="flex items-center gap-2 text-zinc-500 font-semibold text-xs tracking-wider uppercase">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        <span>SECURE PORTAL</span>
                    </div>
                </header>

                <!-- Login/Register Content Container -->
                <div class="flex-grow flex flex-col justify-center px-8 md:px-24 pt-24 pb-16">
                    <div class="max-w-md w-full mx-auto">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer -->
                <footer class="px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-400">
                    <p>© 2026 GAMUT-C. HIPAA Compliant.</p>
                    <div class="flex gap-6">
                        <a class="hover:text-growth-sage transition-colors" href="#">Privacy Policy</a>
                        <a class="hover:text-growth-sage transition-colors" href="#">Terms of Service</a>
                    </div>
                </footer>
            </div>

            <!-- Inspiration / Visual Side -->
            <div class="hidden md:block md:w-1/2 relative overflow-hidden bg-trust-navy">
                <!-- Background Image -->
                <div class="absolute inset-0 z-0 scale-105 transition-transform duration-[10s] ease-linear hover:scale-100 bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAyPybBXqs881jXnHHno0zqEkj4Ac9jhdyaqvHQCJSWBD09NmHHfp5ymeyvrj9RS6Qef5g-vu_jYg-FjauzJYHiC2u9JuNZY_iV-lomKFUuc_6UghlIG4_XoLfbkPeTgzoinEmqQdsvYzqmG0eB6unsYeYZlIaQzTNWtZWFl8YxuRTv62vBsagIUfrK1AucjC466P38XV4bU_hN9HWxoCnfVKgQ-S1TLP3-_y8odj_0bKqRrcdcbyyN34jSGwBzLcFKtGgi7tVTnvE')">
                </div>
                <!-- Overlay Content -->
                <div class="absolute inset-0 bg-gradient-to-t from-trust-navy via-trust-navy/40 to-transparent z-[1] flex flex-col justify-end p-16">
                    <div class="max-w-lg">
                        <span class="inline-block px-3 py-1 bg-growth-sage text-white text-xs font-bold rounded-lg mb-6 uppercase tracking-wider">CLINICAL PRECISION</span>
                        <h2 class="font-headline-xl text-3xl font-bold text-white mb-4 leading-tight">Elevating Neurodevelopmental Care Through Data.</h2>
                        <p class="text-sm text-white/80 leading-relaxed mb-8">
                            The heavy responsibility of clinical decision-making requires unshakeable stability. Bloom provides the tools to minimize cognitive load and maximize patient outcomes.
                        </p>
                        <!-- Quote/Metric Widget -->
                        <div class="flex gap-8 items-center border-t border-white/20 pt-8">
                            <div>
                                <div class="text-xl font-bold text-white">400+</div>
                                <div class="text-[10px] text-white/60 uppercase tracking-wider">Specialized Practices</div>
                            </div>
                            <div class="w-px h-8 bg-white/20"></div>
                            <div>
                                <div class="text-xl font-bold text-white">99.9%</div>
                                <div class="text-[10px] text-white/60 uppercase tracking-wider">Uptime Reliability</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Floating Decoration -->
                <div class="absolute top-12 right-12 z-[2] opacity-20 pointer-events-none">
                    <span class="material-symbols-outlined text-[120px] text-white">psychology</span>
                </div>
            </div>
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
