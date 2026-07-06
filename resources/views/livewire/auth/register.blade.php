<x-layouts::auth :title="__('Register')">
    <div x-data="{ registrationType: 'clinician' }" class="flex flex-col gap-6">
        <!-- Progress Stepper Simulation -->
        <div class="flex items-center gap-3 overflow-x-auto pb-1">
            <div class="flex items-center gap-1.5 shrink-0">
                <div class="w-6 h-6 rounded-full border border-trust-navy text-trust-navy dark:border-zinc-200 dark:text-zinc-200 flex items-center justify-center text-xs font-bold bg-zinc-100 dark:bg-zinc-800">1</div>
                <span class="text-xs font-bold text-trust-navy dark:text-zinc-200">{{ __('Account Setup') }}</span>
            </div>
            <div class="h-px w-6 bg-zinc-250 dark:bg-zinc-800"></div>
            <div class="flex items-center gap-1.5 shrink-0 opacity-40">
                <div class="w-6 h-6 rounded-full border border-zinc-400 flex items-center justify-center text-xs font-semibold">2</div>
                <span class="text-xs">{{ __('Legal & Consent') }}</span>
            </div>
        </div>

        <x-auth-header :title="__('Create your secure account')" :description="__('Register your staff or family portal profile. All data is encrypted and HIPAA compliant.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <!-- Register Switcher -->
        <div class="flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50">
            <button type="button" @click="registrationType = 'clinician'" :class="registrationType === 'clinician' ? 'bg-trust-navy text-white shadow-sm' : 'text-zinc-500 hover:text-trust-navy dark:hover:text-zinc-250'" class="flex-grow flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">medical_services</span>
                <span>{{ __('Clinician Register') }}</span>
            </button>
            <button type="button" @click="registrationType = 'guardian'" :class="registrationType === 'guardian' ? 'bg-trust-navy text-white shadow-sm' : 'text-zinc-500 hover:text-trust-navy dark:hover:text-zinc-250'" class="flex-grow flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">family_restroom</span>
                <span>{{ __('Patient & Family') }}</span>
            </button>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Hidden Role Input -->
            <input type="hidden" name="role" :value="registrationType" />

            <!-- Name -->
            <flux:input
                name="name"
                ::label="registrationType === 'guardian' ? '{{ __('Parent/Guardian Full Name') }}' : '{{ __('Professional Full Name') }}'"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Jane Doe"
            />

            <!-- Practice Name (clinician only) -->
            <template x-if="registrationType === 'clinician'">
                <div class="flex flex-col gap-2">
                    <flux:input
                        name="practice_name"
                        :label="__('Practice / Clinic Name')"
                        :value="old('practice_name')"
                        type="text"
                        required
                        placeholder="Bloom Child Psychiatry"
                        description="{{ __('This will become your practice\'s unique workspace.') }}"
                    />
                </div>
            </template>


            <!-- Child Name (only for guardian role) -->
            <template x-if="registrationType === 'guardian'">
                <div class="flex flex-col gap-2">
                    <flux:input
                        name="child_name"
                        :label="__('Child\'s Full Name')"
                        :value="old('child_name')"
                        type="text"
                        required
                        placeholder="John Doe Jr."
                    />
                </div>
            </template>

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Primary Contact Email')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="jane.doe@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Create Secure Password')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="••••••••••••"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm Secure Password')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="••••••••••••"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end pt-2">
                <flux:button type="submit" variant="primary" class="w-full h-12 bg-growth-sage text-white rounded-xl font-semibold flex items-center justify-center gap-2 hover:bg-[#5f8c69]" data-test="register-user-button">
                    <span>{{ __('Create secure account') }}</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-650 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
