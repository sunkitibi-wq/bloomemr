<x-layouts::auth :title="__('Patient Portal Login')">
    <div class="flex flex-col gap-6">
        <!-- Portal Badge -->
        <div class="flex items-center justify-center">
            <div class="flex items-center gap-2 px-4 py-2 bg-growth-sage/10 border border-growth-sage/30 rounded-full">
                <span class="material-symbols-outlined text-growth-sage text-[18px]" style="font-variation-settings: 'FILL' 1;">family_restroom</span>
                <span class="text-xs font-bold text-growth-sage uppercase tracking-wider">{{ __('Patient & Family Portal') }}</span>
            </div>
        </div>

        <x-auth-header :title="__('Welcome to Your Portal')" :description="__('Sign in to view your health records, appointments, and messages from your care team.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            {{-- Signal to AppServiceProvider to use the portal guard --}}
            <input type="hidden" name="portal_login" value="1" />

            <!-- Email Address -->
            <flux:input
                name="email"
                label="{{ __('Email Address') }}"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="jane.doe@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    label="{{ __('Password') }}"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••••••"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-xs end-0 text-growth-sage hover:underline" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot Password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Keep me signed in')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full h-12 bg-growth-sage text-white rounded-xl font-semibold flex items-center justify-center gap-2 hover:bg-[#5f8c69]" data-test="patient-login-button">
                    <span>{{ __('Access My Portal') }}</span>
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

        <div class="space-y-1 text-sm text-center text-zinc-650 dark:text-zinc-400">
            <div class="space-x-1">
                <span>{{ __("Don't have an account?") }}</span>
                <flux:link :href="route('patient.register')" wire:navigate>{{ __('Register here') }}</flux:link>
            </div>
            <div class="space-x-1">
                <span>{{ __('Are you a staff member?') }}</span>
                <flux:link :href="route('login')" wire:navigate>{{ __('Staff login') }}</flux:link>
            </div>
        </div>
    </div>
</x-layouts::auth>
