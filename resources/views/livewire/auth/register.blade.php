<x-layouts::auth :title="__('Staff Registration')">
    <div x-data="{ role: 'attending' }" class="flex flex-col gap-6">
        
        <x-auth-header :title="__('Create your secure account')" :description="__('Register your staff profile. All data is encrypted and HIPAA compliant.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Role Selection -->
            <div>
                <flux:label>{{ __('Select Your Role') }}</flux:label>
                <div class="flex flex-wrap gap-2 mt-2">
                    @php
                        $roles = [
                            'attending' => 'Doctor / Attending',
                            'resident' => 'Resident Physician',
                            'clinical_staff' => 'Nurse / Clinical Staff',
                            'receptionist' => 'Receptionist',
                            'laboratory_officer' => 'Laboratory Officer',
                            'pharmacist' => 'Pharmacist',
                            'billing_admin' => 'Billing Admin',
                            'medical_records_officer' => 'Medical Records',
                            'radiologist' => 'Radiologist',
                            'insurance_officer' => 'Insurance Officer',
                            'public_health_officer' => 'Public Health',
                            'hospital_administrator' => 'Administrator',
                        ];
                    @endphp
                    @foreach($roles as $key => $label)
                        <button type="button" 
                                @click="role = '{{ $key }}'" 
                                :class="role === '{{ $key }}' ? 'bg-trust-navy text-white border-trust-navy shadow-sm' : 'bg-zinc-50 text-zinc-600 border-zinc-200 hover:bg-zinc-100 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-700'" 
                                class="px-3 py-1.5 rounded-full border text-xs font-medium transition-colors">
                            {{ __($label) }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="role" :value="role" />
            </div>

            <!-- Name -->
            <flux:input
                name="name"
                label="{{ __('Professional Full Name') }}"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Jane Doe"
            />

            <!-- Practice Name -->
            <div class="flex flex-col gap-2">
                <flux:input
                    name="practice_name"
                    label="{{ __('Practice / Clinic Name (Optional)') }}"
                    :value="old('practice_name')"
                    type="text"
                    placeholder="Bloom Clinic"
                    description="{{ __('Enter a name to create a new practice, or leave blank to join an existing one.') }}"
                />
            </div>

            <!-- Email Address -->
            <flux:input
                name="email"
                label="{{ __('Primary Contact Email') }}"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="jane.doe@example.com"
            />

            <!-- Password -->
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

            <!-- Confirm Password -->
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
                <flux:button type="submit" variant="primary" class="w-full h-12 bg-growth-sage text-white rounded-xl font-semibold flex items-center justify-center gap-2 hover:bg-[#5f8c69]" data-test="register-user-button">
                    <span>{{ __('Create secure account') }}</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-650 dark:text-zinc-400">
            <span>{{ __('Are you a patient or family member?') }}</span>
            <flux:link :href="route('patient.register')" wire:navigate>{{ __('Register here') }}</flux:link>
        </div>
        
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-650 dark:text-zinc-400 -mt-3">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
