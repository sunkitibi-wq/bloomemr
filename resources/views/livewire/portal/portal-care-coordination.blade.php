<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col md:flex-row">
    <!-- Sidebar Navigation -->
    <nav class="w-full md:w-64 bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-800 flex flex-col p-6 shrink-0">
        <!-- Logo -->
        <div class="flex items-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white font-bold">
                B
            </div>
            <div>
                <span class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 tracking-tight text-base">Bloom</span>
                <span class="text-[9px] text-zinc-400 block -mt-1 uppercase tracking-wider">{{ __('Patient Portal') }}</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 space-y-1">
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.dashboard') }}">
                <span class="material-symbols-outlined text-xl">home</span>
                <span class="text-sm">{{ __('Home') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.appointments') }}">
                <span class="material-symbols-outlined text-xl">calendar_today</span>
                <span class="text-sm">{{ __('Appointments') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.billing') }}">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
                <span class="text-sm">{{ __('Billing & Invoices') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.messages') }}">
                <span class="material-symbols-outlined text-xl">mail</span>
                <span class="text-sm">{{ __('Secure Messages') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.refills') }}">
                <span class="material-symbols-outlined text-xl">vaccines</span>
                <span class="text-sm">{{ __('Refill Requests') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.telehealth') }}">
                <span class="material-symbols-outlined text-xl">videocam</span>
                <span class="text-sm">{{ __('Telehealth Room') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.forms') }}">
                <span class="material-symbols-outlined text-xl">description</span>
                <span class="text-sm">{{ __('Intake & Consents') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800 rounded-lg" href="{{ route('portal.care-coordination') }}">
                <span class="material-symbols-outlined text-xl">share</span>
                <span class="text-sm">{{ __('Care Sharing Log') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.radiology') }}">
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

    <!-- Main Content Area -->
    <main class="flex-1 p-6 md:p-8 flex flex-col overflow-y-auto">
        <div class="max-w-6xl w-full mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold font-heading text-trust-navy dark:text-zinc-100">{{ __('Clinical Care Sharing History') }}</h1>
                    <p class="text-sm text-zinc-500 mt-1">{{ __('Records of clinical information shared securely with external entities.') }}</p>
                </div>
            </div>

            <!-- Disclaimer alert box -->
            <div class="p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800 rounded-2xl flex gap-3 text-sm text-blue-700 dark:text-blue-300">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">info</span>
                <div>
                    <p class="font-semibold">{{ __('Notice of Data Release') }}</p>
                    <p class="mt-1 text-xs opacity-90 leading-relaxed">
                        {{ __('Under HIPAA guidelines, you have the right to request an accounting of disclosures of your protected health info (PHI). This log details every Continuity of Care Document (CCD) or referral shared by our practice with school systems, therapists, or primary care physicians on your behalf, authenticated by your signed consent form.') }}
                    </p>
                </div>
            </div>

            <!-- Sharing Log Table -->
            @if ($patient)
                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                            <thead class="bg-slate-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Shared With / Institution') }}</th>
                                    <th class="px-6 py-4">{{ __('Direct Address') }}</th>
                                    <th class="px-6 py-4">{{ __('Document Scope') }}</th>
                                    <th class="px-6 py-4">{{ __('Signed Release Form') }}</th>
                                    <th class="px-6 py-4">{{ __('Release Date') }}</th>
                                    <th class="px-6 py-4">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                                @forelse ($messages as $msg)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-850/20 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                            {{ $msg->recipient_name }}
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            {{ $msg->recipient_address }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-zinc-800 text-slate-800 dark:text-slate-200">
                                                {{ $msg->scope }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <span class="font-medium text-zinc-800 dark:text-zinc-200 block">{{ $msg->consent->template->title }}</span>
                                            <span class="text-zinc-400 block mt-0.5">Signed: {{ $msg->consent->completed_at?->format('Y-m-d') }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-zinc-400">
                                            {{ $msg->created_at->format('M j, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-300">
                                                {{ ucfirst($msg->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-zinc-400 italic">
                                            {{ __('No clinical records have been shared under your release of information (ROI) authorizations yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl p-12 text-center text-zinc-400 italic">
                    {{ __('No patient record associated with this account.') }}
                </div>
            @endif
        </div>
    </main>
</div>
