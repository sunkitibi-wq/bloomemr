<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Clinical Care Sharing History') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1"></p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
        <div class="max-w-6xl w-full mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold font-heading text-slate-800 ">{{ __('Clinical Care Sharing History') }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ __('Records of clinical information shared securely with external entities.') }}</p>
                </div>
            </div>

            <!-- Disclaimer alert box -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl flex gap-3 text-sm text-blue-700 ">
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
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-500 ">
                            <thead class="bg-slate-50 text-xs text-slate-700 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Shared With / Institution') }}</th>
                                    <th class="px-6 py-4">{{ __('Direct Address') }}</th>
                                    <th class="px-6 py-4">{{ __('Document Scope') }}</th>
                                    <th class="px-6 py-4">{{ __('Signed Release Form') }}</th>
                                    <th class="px-6 py-4">{{ __('Release Date') }}</th>
                                    <th class="px-6 py-4">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 ">
                                @forelse ($messages as $msg)
                                    <tr class="hover:bg-slate-50/50 hover:bg-slate-100 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-slate-900 ">
                                            {{ $msg->recipient_name }}
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            {{ $msg->recipient_address }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 ">
                                                {{ $msg->scope }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <span class="font-medium text-slate-800 block">{{ $msg->consent->template->title }}</span>
                                            <span class="text-slate-500 block mt-0.5">Signed: {{ $msg->consent->completed_at?->format('Y-m-d') }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500">
                                            {{ $msg->created_at->format('M j, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 ">
                                                {{ ucfirst($msg->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">
                                            {{ __('No clinical records have been shared under your release of information (ROI) authorizations yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-500 italic">
                    {{ __('No patient record associated with this account.') }}
                </div>
            @endif
        </div>
    </div>
