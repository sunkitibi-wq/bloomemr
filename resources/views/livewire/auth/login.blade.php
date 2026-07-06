<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Welcome Back')" :description="__('Please enter your credentials to access the clinical ecosystem.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify />

        <!-- Role Switcher Simulation -->
        <div class="flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50">
            <button type="button" class="flex-grow flex items-center justify-center gap-2 py-2.5 rounded-lg text-xs font-semibold bg-trust-navy text-white shadow-sm">
                <span class="material-symbols-outlined text-[16px]">medical_services</span>
                <span>{{ __('Clinician Login') }}</span>
            </button>
            <a href="{{ route('portal.dashboard') }}" class="flex-grow flex items-center justify-center gap-2 py-2.5 rounded-lg text-xs font-semibold text-zinc-500 hover:text-trust-navy dark:hover:text-zinc-200 hover:bg-zinc-250 dark:hover:bg-zinc-700 transition-colors">
                <span class="material-symbols-outlined text-[16px]">family_restroom</span>
                <span>{{ __('Patient & Family') }}</span>
            </a>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Professional Email')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="doctor@bloom.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Secure Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-xs end-0 text-growth-sage hover:underline" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot Password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember this device for 30 days')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full h-12 bg-trust-navy dark:bg-zinc-150 hover:bg-trust-navy/90 text-white dark:text-zinc-900 rounded-xl font-semibold flex items-center justify-center gap-2" data-test="login-button">
                    <span>{{ __('Sign In to Workflow') }}</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </flux:button>
            </div>
        </form>

        <!-- Trust Indicators -->
        <div class="pt-6 border-t border-zinc-200/50 dark:border-zinc-700/50 flex flex-wrap gap-3 items-center justify-center">
            <div class="flex items-center gap-1 px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full border border-emerald-500/20 text-[10px] font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">verified</span>
                <span>{{ __('HIPAA COMPLIANT') }}</span>
            </div>
            <div class="flex items-center gap-1 px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-650 dark:text-zinc-300 rounded-full border border-zinc-200 dark:border-zinc-700 text-[10px] font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-xs">lock</span>
                <span>{{ __('256-BIT ENCRYPTION') }}</span>
            </div>
        </div>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-650 dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
