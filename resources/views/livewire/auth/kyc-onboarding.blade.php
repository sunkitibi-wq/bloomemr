<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col justify-between py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full mx-auto bg-white dark:bg-zinc-900 shadow-xl rounded-3xl p-8 border border-slate-100 dark:border-zinc-800">
        <!-- Logo Header -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-growth-sage flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-growth-sage/20">
                G
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-trust-navy dark:text-zinc-150">
                {{ __('Clinical Identity Verification') }}
            </h2>
            <p class="mt-1 text-center text-xs text-zinc-405 dark:text-zinc-400">
                {{ __('To access patient records and clinical modules, you must verify your identity.') }}
            </p>
        </div>

        @if (auth()->user()->kyc_status === 'rejected')
            <!-- Rejection Alert -->
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800/30 rounded-2xl flex items-start gap-3">
                <span class="material-symbols-outlined text-red-500 text-xl shrink-0 mt-0.5">warning</span>
                <div>
                    <h4 class="text-sm font-bold text-red-800 dark:text-red-400">{{ __('Verification Rejected') }}</h4>
                    <p class="text-xs text-red-700 dark:text-red-300 mt-0.5">
                        {{ auth()->user()->kyc_rejection_reason ?: __('The details or documents provided could not be verified.') }}
                    </p>
                    <flux:button variant="ghost" size="sm" class="mt-3 text-red-650 hover:bg-red-100/50" wire:click="restartKyc">
                        {{ __('Fix and Resubmit') }}
                    </flux:button>
                </div>
            </div>
        @endif

        @if ($step === 4)
            <!-- Pending Screen -->
            <div class="flex flex-col items-center py-6 text-center">
                <div class="w-16 h-16 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">hourglass_empty</span>
                </div>
                <h3 class="text-lg font-bold text-trust-navy dark:text-zinc-150">{{ __('Verification In Progress') }}</h3>
                <p class="text-sm text-zinc-500 mt-2 max-w-sm">
                    {{ __('We are currently verifying your professional medical credentials against the national provider registry. This typically takes 1 to 2 business hours.') }}
                </p>
                <div class="mt-8 w-full border-t border-slate-100 dark:border-zinc-800 pt-6 flex flex-col gap-3">
                    <div class="flex justify-between text-xs text-zinc-400">
                        <span>{{ __('Verification Status') }}</span>
                        <flux:badge size="sm" color="amber">{{ __('Pending Audit') }}</flux:badge>
                    </div>
                    <div class="flex justify-between text-xs text-zinc-400">
                        <span>{{ __('Submitted At') }}</span>
                        <span>{{ now()->format('M j, Y H:i') }}</span>
                    </div>
                </div>

                <div class="mt-8 flex justify-center w-full">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:button variant="ghost" type="submit" class="w-full">
                            {{ __('Log Out') }}
                        </flux:button>
                    </form>
                </div>
            </div>
        @else
            <!-- Onboarding steps indicators -->
            <div class="flex items-center justify-between mb-8 px-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $step >= 1 ? 'bg-growth-sage text-white' : 'bg-slate-100 text-zinc-400' }}">1</div>
                    <span class="text-xs font-semibold {{ $step == 1 ? 'text-trust-navy font-bold' : 'text-zinc-400' }}">{{ __('Personal') }}</span>
                </div>
                <div class="w-8 h-px bg-slate-200 dark:bg-zinc-800"></div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $step >= 2 ? 'bg-growth-sage text-white' : 'bg-slate-100 text-zinc-400' }}">2</div>
                    <span class="text-xs font-semibold {{ $step == 2 ? 'text-trust-navy font-bold' : 'text-zinc-400' }}">{{ __('License') }}</span>
                </div>
                <div class="w-8 h-px bg-slate-200 dark:bg-zinc-800"></div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $step >= 3 ? 'bg-growth-sage text-white' : 'bg-slate-100 text-zinc-400' }}">3</div>
                    <span class="text-xs font-semibold {{ $step == 3 ? 'text-trust-navy font-bold' : 'text-zinc-400' }}">{{ __('Docs') }}</span>
                </div>
            </div>

            <!-- Onboarding form -->
            <div class="space-y-6">
                @if ($step === 1)
                    <!-- Step 1: Personal info -->
                    <flux:input wire:model="legal_first_name" label="{{ __('Legal First Name') }}" required />
                    <flux:input wire:model="legal_last_name" label="{{ __('Legal Last Name') }}" required />
                    <flux:input wire:model="date_of_birth" type="date" label="{{ __('Date of Birth') }}" required />

                    <flux:select wire:model="selected_role" label="{{ __('Professional Role') }}" placeholder="{{ __('Select role') }}">
                        <option value="attending">{{ __('Attending Physician') }}</option>
                        <option value="resident">{{ __('Resident Physician') }}</option>
                        <option value="pharmacist">{{ __('Pharmacist') }}</option>
                        <option value="clinical_staff">{{ __('Clinical Staff / Nurse') }}</option>
                        <option value="hospital_administrator">{{ __('Hospital Administrator') }}</option>
                        <option value="billing_admin">{{ __('Billing Admin') }}</option>
                        <option value="radiologist">{{ __('Radiologist') }}</option>
                    </flux:select>

                @elseif ($step === 2)
                    <!-- Step 2: Credentials -->
                    <flux:input wire:model="license_number" label="{{ __('State License Number') }}" placeholder="e.g. LIC123456" required />
                    <flux:input wire:model="license_state" label="{{ __('License State of Issue') }}" placeholder="e.g. NY" max="2" required />
                    <flux:input wire:model="npi_number" label="{{ __('NPI Number (10 digits)') }}" placeholder="e.g. 1234567890" />

                @elseif ($step === 3)
                    <!-- Step 3: Document mock upload -->
                    <div class="text-center py-8 px-4 border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-3xl flex flex-col items-center">
                        <span class="material-symbols-outlined text-4xl text-zinc-300 mb-2">upload_file</span>
                        <h4 class="text-sm font-semibold text-trust-navy dark:text-zinc-150">{{ __('Upload Professional ID / License') }}</h4>
                        <p class="text-xs text-zinc-400 mt-1 max-w-xs">{{ __('Please upload a clear copy of your state medical license certificate or photo ID for manual compliance audit.') }}</p>

                        @if ($document_uploaded)
                            <div class="mt-4 p-2 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250 rounded-xl flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-500 text-sm">check_circle</span>
                                <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-400">{{ $document_name }}</span>
                            </div>
                        @else
                            <flux:button variant="ghost" size="sm" class="mt-4 text-primary" wire:click="uploadMockDocument">
                                {{ __('Attach Document Mock') }}
                            </flux:button>
                            @error('document')
                                <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>
                @endif

                <!-- Navigation buttons -->
                <div class="pt-6 border-t border-slate-100 dark:border-zinc-800 flex justify-between gap-4">
                    @if ($step > 1)
                        <flux:button variant="ghost" class="flex-1" wire:click="prevStep">
                            {{ __('Back') }}
                        </flux:button>
                    @else
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <flux:button variant="ghost" type="submit" class="w-full">
                                {{ __('Cancel') }}
                            </flux:button>
                        </form>
                    @endif

                    @if ($step < 3)
                        <flux:button variant="primary" class="flex-1 bg-growth-sage hover:bg-growth-sage/90" wire:click="nextStep">
                            {{ __('Continue') }}
                        </flux:button>
                    @else
                        <flux:button variant="primary" class="flex-1 bg-growth-sage hover:bg-growth-sage/90" wire:click="submitKyc">
                            {{ __('Submit for Verification') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
