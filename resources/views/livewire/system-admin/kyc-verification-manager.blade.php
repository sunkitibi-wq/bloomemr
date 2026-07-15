<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold font-heading text-trust-navy dark:text-zinc-100">{{ __('KYC Compliance Dashboard') }}</h1>
            <p class="text-sm text-zinc-400 mt-1">{{ __('Manage and audit professional clinical identity verification requests.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="ghost" href="{{ route('system.practices') }}" icon="arrow-left" wire:navigate>
                {{ __('Back to Practices') }}
            </flux:button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-slate-200 dark:border-zinc-800 mb-6">
        <button 
            wire:click="$set('activeTab', 'pending')"
            class="py-3 px-6 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'pending' ? 'border-growth-sage text-growth-sage' : 'border-transparent text-zinc-400 hover:text-zinc-300' }}">
            {{ __('Pending Verification') }} ({{ count($this->pendingUsers) }})
        </button>
        <button 
            wire:click="$set('activeTab', 'history')"
            class="py-3 px-6 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'history' ? 'border-growth-sage text-growth-sage' : 'border-transparent text-zinc-400 hover:text-zinc-300' }}">
            {{ __('Audit History') }} ({{ count($this->historyUsers) }})
        </button>
    </div>

    <!-- Content -->
    @if ($activeTab === 'pending')
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-slate-100 dark:border-zinc-800 overflow-hidden">
            @if (count($this->pendingUsers) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zinc-800/40 text-xs font-bold text-zinc-400 uppercase tracking-wider">
                                <th class="p-4">{{ __('User & Role') }}</th>
                                <th class="p-4">{{ __('Legal Details') }}</th>
                                <th class="p-4">{{ __('Medical License') }}</th>
                                <th class="p-4">{{ __('NPI') }}</th>
                                <th class="p-4">{{ __('Submitted Document') }}</th>
                                <th class="p-4 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-sm">
                            @foreach ($this->pendingUsers as $user)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20">
                                    <td class="p-4">
                                        <div class="font-semibold text-trust-navy dark:text-zinc-200">{{ $user->name }}</div>
                                        <div class="text-xs text-zinc-400">{{ $user->email }}</div>
                                        <flux:badge size="sm" color="zinc" class="mt-1">{{ ucfirst($user->role) }}</flux:badge>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-medium text-trust-navy dark:text-zinc-200">
                                            {{ $user->kyc_data['legal_first_name'] ?? '' }} {{ $user->kyc_data['legal_last_name'] ?? '' }}
                                        </div>
                                        <div class="text-xs text-zinc-400">{{ __('DOB: :dob', ['dob' => isset($user->kyc_data['date_of_birth']) ? date('M j, Y', strtotime($user->kyc_data['date_of_birth'])) : '']) }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-semibold font-mono text-zinc-700 dark:text-zinc-350">{{ $user->kyc_data['license_number'] ?? 'N/A' }}</div>
                                        <div class="text-xs text-zinc-400">{{ __('State: :state', ['state' => $user->kyc_data['license_state'] ?? 'N/A']) }}</div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-650 dark:text-zinc-400">
                                        {{ $user->kyc_data['npi_number'] ?? __('None') }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1.5 text-xs text-primary font-semibold hover:underline cursor-pointer">
                                            <span class="material-symbols-outlined text-base">attachment</span>
                                            <span>{{ $user->kyc_data['document_name'] ?? __('View Document') }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex flex-col gap-2 items-end">
                                            <div class="flex gap-2">
                                                <flux:button size="sm" variant="primary" class="bg-growth-sage hover:bg-growth-sage/90" wire:click="approveKyc({{ $user->id }})">
                                                    {{ __('Approve') }}
                                                </flux:button>
                                                <flux:button size="sm" variant="danger" wire:click="rejectKyc({{ $user->id }})">
                                                    {{ __('Reject') }}
                                                </flux:button>
                                            </div>
                                            <div class="w-48 mt-1">
                                                <flux:input 
                                                    size="sm" 
                                                    wire:model="rejectionReasons.{{ $user->id }}" 
                                                    placeholder="{{ __('Reason for rejection') }}" 
                                                />
                                                @error("rejectionReasons.{$user->id}")
                                                    <span class="text-[10px] text-red-500 block text-left mt-0.5">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center text-zinc-400 italic">
                    <span class="material-symbols-outlined text-4xl mb-2 text-zinc-300">verified</span>
                    <p>{{ __('No pending KYC verification requests found.') }}</p>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-slate-100 dark:border-zinc-800 overflow-hidden">
            @if (count($this->historyUsers) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zinc-800/40 text-xs font-bold text-zinc-400 uppercase tracking-wider">
                                <th class="p-4">{{ __('User & Role') }}</th>
                                <th class="p-4">{{ __('Legal Details') }}</th>
                                <th class="p-4">{{ __('Medical License') }}</th>
                                <th class="p-4">{{ __('Status') }}</th>
                                <th class="p-4">{{ __('Notes / Rejection Reason') }}</th>
                                <th class="p-4 text-right">{{ __('Audited At') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-sm">
                            @foreach ($this->historyUsers as $user)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20">
                                    <td class="p-4">
                                        <div class="font-semibold text-trust-navy dark:text-zinc-200">{{ $user->name }}</div>
                                        <div class="text-xs text-zinc-400">{{ $user->email }}</div>
                                        <flux:badge size="sm" color="zinc" class="mt-1">{{ ucfirst($user->role) }}</flux:badge>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-medium text-trust-navy dark:text-zinc-200">
                                            {{ $user->kyc_data['legal_first_name'] ?? '' }} {{ $user->kyc_data['legal_last_name'] ?? '' }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-semibold font-mono text-zinc-700 dark:text-zinc-350">{{ $user->kyc_data['license_number'] ?? 'N/A' }}</div>
                                        <div class="text-xs text-zinc-400">{{ __('State: :state', ['state' => $user->kyc_data['license_state'] ?? 'N/A']) }}</div>
                                    </td>
                                    <td class="p-4">
                                        @if ($user->kyc_status === 'approved')
                                            <flux:badge size="sm" color="green" class="font-bold">{{ __('Approved') }}</flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" class="font-bold">{{ __('Rejected') }}</flux:badge>
                                        @endif
                                    </td>
                                    <td class="p-4 max-w-xs text-xs text-zinc-500">
                                        {{ $user->kyc_rejection_reason ?: __('Verified by system admin.') }}
                                    </td>
                                    <td class="p-4 text-right text-xs text-zinc-450">
                                        {{ $user->updated_at->format('M j, Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center text-zinc-400 italic">
                    <p>{{ __('No verification history found.') }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
