<x-layouts::auth :title="__('Hospital Admin Portal Login')">
    <div class="flex flex-col gap-6">
        <!-- Portal Badge -->
        <div class="flex items-center justify-center">
            <div class="flex items-center gap-2 px-4 py-2 bg-rose-50 border border-rose-200 rounded-full dark:bg-rose-900/20 dark:border-rose-700/40">
                <span class="material-symbols-outlined text-rose-700 text-[18px]" style="font-variation-settings: 'FILL' 1;">manage_accounts</span>
                <span class="text-xs font-bold text-rose-700 uppercase tracking-wider dark:text-rose-400">{{ __('Hospital Administration Portal') }}</span>
            </div>
        </div>

        <x-auth-header :title="__('Administration Portal')" :description="__('Sign in to manage your practice, users, analytics, and system-wide settings.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf
            <input type="hidden" name="portal_type" value="hospital" />

            <!-- Email Address -->
            <flux:input
                name="email"
                label="{{ __('Administrator Email') }}"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="admin@hospital.com"
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
                    <flux:link class="absolute top-0 text-xs end-0 text-rose-600 hover:underline" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot Password?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" :label="__('Remember this device for 30 days')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full h-12 bg-rose-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2 hover:bg-rose-800 transition-colors" data-test="hospital-login-button">
                    <span>{{ __('Access Administration Portal') }}</span>
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
            <div class="flex flex-wrap justify-center gap-x-3 gap-y-1">
                <flux:link :href="route('login')" wire:navigate class="text-xs">Staff Login</flux:link>
                <span class="text-zinc-300 dark:text-zinc-600">|</span>
                <flux:link :href="route('doctor.login')" wire:navigate class="text-xs">Doctor Portal</flux:link>
                <span class="text-zinc-300 dark:text-zinc-600">|</span>
                <flux:link :href="route('pharmacy.login')" wire:navigate class="text-xs">Pharmacy Portal</flux:link>
                <span class="text-zinc-300 dark:text-zinc-600">|</span>
                <flux:link :href="route('patient.login')" wire:navigate class="text-xs">Patient Portal</flux:link>
            </div>
        </div>
    </div>
</x-layouts::auth>
