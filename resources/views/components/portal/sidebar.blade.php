@php
    $patient = auth('portal')->check() && auth('portal')->user()->role === 'guardian'
        ? \App\Models\Patient::where('portal_user_id', auth('portal')->id())->first()
        : \App\Models\Patient::where('practice_id', auth()->user()?->practice_id)->first();
@endphp
<aside class="flex-none bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-all duration-300 overflow-hidden whitespace-nowrap" :class="leftCollapsed ? 'w-0 opacity-0 border-none' : 'w-[260px] opacity-100'">
    <!-- Branding -->
    <div class="h-20 flex items-center px-6 shrink-0 mt-2">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg">
            {{ $patient ? substr($patient->first_name, 0, 1) : 'B' }}
        </div>
        <span class="ml-3 font-bold text-xl text-slate-800 dark:text-slate-100 tracking-tight leading-tight">
            {{ $patient ? $patient->first_name . "'s Care" : 'Bloom' }}<br>
            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-widest leading-none">Patient Portal</span>
        </span>
    </div>

    <!-- Scrollable Nav -->
    <div class="flex-1 overflow-y-auto py-4 px-4 flex flex-col gap-8 custom-scrollbar">
        
        <!-- Quick Access -->
        <div>
            <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3 px-2">Quick Access</div>
            <nav class="space-y-1">
                <a href="{{ route('portal.appointments') }}" class="flex items-center justify-between px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.appointments') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <flux:icon.calendar variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.appointments') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Appointments
                    </div>
                </a>
                <a href="{{ route('portal.messages') }}" class="flex items-center justify-between px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.messages') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <flux:icon.chat-bubble-left-ellipsis variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.messages') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Secure Messages
                    </div>
                </a>
                <a href="{{ route('portal.labs') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.labs') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <flux:icon.beaker variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.labs') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Lab Results
                </a>
                <a href="{{ route('portal.radiology') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.radiology') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <flux:icon.document-magnifying-glass variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.radiology') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Imaging Reports
                </a>
            </nav>
        </div>

        <!-- Main Nav -->
        <div>
            <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3 px-2">Main Menu</div>
            <nav class="space-y-1 mb-8">
                <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.dashboard') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50 relative overflow-hidden font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    @if(request()->routeIs('portal.dashboard'))
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 rounded-r-md"></div>
                        <flux:icon.home variant="solid" class="w-[18px] h-[18px] text-indigo-600 dark:text-indigo-400" />
                    @else
                        <flux:icon.home variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" />
                    @endif
                    Home
                </a>
                <a href="{{ route('portal.billing') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.billing') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <flux:icon.banknotes variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.billing') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Billing & Invoices
                </a>
                <a href="{{ route('portal.triage') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.triage') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <flux:icon.heart variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.triage') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Symptom Triage
                </a>
                <a href="{{ route('portal.refills') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.refills') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <flux:icon.beaker variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.refills') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Refill Requests
                </a>
                <a href="{{ route('portal.telehealth') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.telehealth') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <flux:icon.video-camera variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.telehealth') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Telehealth Room
                </a>
                <a href="{{ route('portal.forms') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.forms') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <flux:icon.document-text variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.forms') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Intake & Consents
                </a>
                <a href="{{ route('portal.care-coordination') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium {{ request()->routeIs('portal.care-coordination') ? 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/50' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none' }} rounded-lg transition-all duration-200">
                    <flux:icon.share variant="outline" class="w-[18px] h-[18px] {{ request()->routeIs('portal.care-coordination') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" /> Care Sharing Log
                </a>
            </nav>
        </div>

    </div>

    <!-- Footer -->
    <div class="p-4 shrink-0 mb-4">
        <button class="w-full py-2.5 px-4 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg flex items-center justify-center gap-2 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors mb-4 border border-red-100 dark:border-red-900/30">
            <flux:icon.phone variant="solid" class="w-4 h-4" />
            <span>Emergency Contact</span>
        </button>

        <a class="flex items-center gap-3 px-4 py-2 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700" href="{{ route('dashboard') }}" wire:navigate>
            <flux:icon.arrow-right-start-on-rectangle variant="outline" class="w-4 h-4" />
            <span class="text-xs font-bold">Back to EMR</span>
        </a>
    </div>
</aside>
