    @php
        $patient = auth('portal')->check() && auth('portal')->user()->role === 'guardian'
            ? \App\Models\Patient::where('portal_user_id', auth('portal')->id())->first()
            : \App\Models\Patient::where('practice_id', auth()->user()?->practice_id)->first();
    @endphp
    <nav class="w-full md:w-64 shrink-0 flex flex-col p-5 bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-800">
        <!-- Logo -->
        <div class="flex items-center gap-2.5 mb-8 px-1">
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white font-bold">
                {{ $patient ? substr($patient->first_name, 0, 1) : 'B' }}
            </div>
            <div>
                <span class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 tracking-tight text-base">{{ $patient ? $patient->first_name . "'s Care" : 'Bloom' }}</span>
                <span class="text-[9px] text-zinc-400 block -mt-1 uppercase tracking-wider">{{ __('Patient Portal') }}</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 space-y-1">
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.dashboard') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.dashboard') }}">
                <span class="material-symbols-outlined text-xl">home</span>
                <span class="text-sm">{{ __('Home') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.appointments') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.appointments') }}">
                <span class="material-symbols-outlined text-xl">calendar_today</span>
                <span class="text-sm">{{ __('Appointments') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.billing') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.billing') }}">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
                <span class="text-sm">{{ __('Billing & Invoices') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.messages') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.messages') }}">
                <span class="material-symbols-outlined text-xl">mail</span>
                <span class="text-sm">{{ __('Secure Messages') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.labs') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.labs') }}">
                <span class="material-symbols-outlined text-xl">science</span>
                <span class="text-sm">{{ __('Lab Results') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.triage') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.triage') }}">
                <span class="material-symbols-outlined text-xl">health_and_safety</span>
                <span class="text-sm">{{ __('Symptom Triage') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.refills') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.refills') }}">
                <span class="material-symbols-outlined text-xl">vaccines</span>
                <span class="text-sm">{{ __('Refill Requests') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.telehealth') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.telehealth') }}">
                <span class="material-symbols-outlined text-xl">videocam</span>
                <span class="text-sm">{{ __('Telehealth Room') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.forms') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.forms') }}">
                <span class="material-symbols-outlined text-xl">description</span>
                <span class="text-sm">{{ __('Intake & Consents') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.care-coordination') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.care-coordination') }}">
                <span class="material-symbols-outlined text-xl">share</span>
                <span class="text-sm">{{ __('Care Sharing Log') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('portal.radiology') ? 'text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800' : 'text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}" href="{{ route('portal.radiology') }}">
                <span class="material-symbols-outlined text-xl">biotech</span>
                <span class="text-sm">{{ __('Imaging Reports') }}</span>
            </a>
        </div>

        <!-- CTA & Exit -->
        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-zinc-800 space-y-3">
            <button class="w-full py-2.5 px-4 bg-status-critical text-white text-xs font-bold rounded-lg flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">emergency</span>
                <span>{{ __('Emergency Contact') }}</span>
            </button>
            
            <a class="flex items-center gap-3 px-4 py-2 text-zinc-500 dark:text-zinc-400 hover:text-trust-navy dark:hover:text-zinc-100 transition-colors" href="{{ route('dashboard') }}" wire:navigate>
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                <span class="text-xs">{{ __('Back to EMR') }}</span>
            </a>
        </div>
    </nav>
