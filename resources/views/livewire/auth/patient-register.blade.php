<x-layouts::auth :title="__('Patient Registration')">
    <div x-data="{ step: 1 }" class="flex flex-col gap-6">
        <!-- Progress Stepper -->
        <div class="flex items-center gap-3 overflow-x-auto pb-1">
            <div class="flex items-center gap-1.5 shrink-0">
                <div class="w-6 h-6 rounded-full border border-trust-navy text-trust-navy dark:border-zinc-200 dark:text-zinc-200 flex items-center justify-center text-xs font-bold bg-zinc-100 dark:bg-zinc-800">1</div>
                <span class="text-xs font-bold text-trust-navy dark:text-zinc-200">{{ __('Account Setup') }}</span>
            </div>
            <div class="h-px w-6 bg-zinc-250 dark:bg-zinc-800"></div>
            <div class="flex items-center gap-1.5 shrink-0" :class="step === 2 ? 'opacity-100 text-trust-navy dark:text-zinc-200' : 'opacity-40'">
                <div class="w-6 h-6 rounded-full border flex items-center justify-center text-xs font-semibold" :class="step === 2 ? 'border-trust-navy bg-zinc-100 dark:bg-zinc-800' : 'border-zinc-400'">2</div>
                <span class="text-xs" :class="step === 2 ? 'font-bold' : ''">{{ __('Legal & Consent') }}</span>
            </div>
        </div>

        <x-auth-header :title="__('Patient Portal Registration')" :description="__('Register your family portal profile. All data is encrypted and HIPAA compliant.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Hidden Role -->
            <input type="hidden" name="role" value="guardian" />

            <!-- Step 1: Account Setup -->
            <div x-show="step === 1" class="flex flex-col gap-5">
                <flux:input
                    name="name"
                    label="{{ __('Parent/Guardian Full Name') }}"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Jane Doe"
                />

                <flux:input
                    name="child_name"
                    label="{{ __('Child\'s Full Name') }}"
                    :value="old('child_name')"
                    type="text"
                    required
                    placeholder="John Doe Jr."
                />

                <flux:input
                    name="email"
                    label="{{ __('Primary Contact Email') }}"
                    :value="old('email')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="jane.doe@example.com"
                />

                <flux:input
                    name="password"
                    label="{{ __('Create Secure Password') }}"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••••••"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    viewable
                />

                <flux:input
                    name="password_confirmation"
                    label="{{ __('Confirm Secure Password') }}"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••••••"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    viewable
                />

                <div class="flex items-center justify-end pt-2">
                    <flux:button type="button" @click="step = 2" variant="primary" class="w-full h-12 bg-trust-navy text-white rounded-xl font-semibold flex items-center justify-center gap-2 hover:bg-[#1a2c42]">
                        <span>{{ __('Next step') }}</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </flux:button>
                </div>
            </div>

            <!-- Step 2: Legal & Consent -->
            <div x-show="step === 2" class="flex flex-col gap-5" style="display: none;" x-cloak>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 text-sm text-zinc-600 dark:text-zinc-300">
                    <p class="font-semibold mb-2 text-trust-navy dark:text-zinc-200">{{ __('Notice of Privacy Practices & HIPAA Consent') }}</p>
                    <p class="mb-2 leading-relaxed">By checking the box below, you acknowledge that you have reviewed and agree to our HIPAA Privacy Practices. Your health information will be kept strictly confidential.</p>
                </div>

                <flux:checkbox
                    name="terms"
                    id="terms"
                    label="{{ __('I agree to the Terms of Service and Privacy Policy') }}"
                />

                <div class="flex items-center gap-3 pt-2">
                    <flux:button type="button" @click="step = 1" variant="ghost" class="h-12 px-4 rounded-xl font-semibold flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        <span>{{ __('Back') }}</span>
                    </flux:button>
                    <flux:button type="submit" variant="primary" class="flex-grow h-12 bg-growth-sage text-white rounded-xl font-semibold flex items-center justify-center gap-2 hover:bg-[#5f8c69]" data-test="register-user-button">
                        <span>{{ __('Create secure account') }}</span>
                        <span class="material-symbols-outlined text-sm">check</span>
                    </flux:button>
                </div>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-650 dark:text-zinc-400">
            <span>{{ __('Are you a staff member?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Register here') }}</flux:link>
        </div>
        
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-650 dark:text-zinc-400 -mt-3">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
