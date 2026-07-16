<flux:main>
    <flux:heading size="xl" level="1" class="mb-8">
        {{ $editing ? __('Edit Patient') : __('New Patient') }}
    </flux:heading>

    <!-- Progress Indicator -->
    <div class="mb-8 max-w-2xl">
        <div class="flex items-center justify-between">
            <!-- Step 1: Demographics -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors {{ $currentStep >= 1 ? 'bg-primary text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500' }}">
                    @if ($currentStep > 1)
                        <flux:icon.check class="size-4" />
                    @else
                        1
                    @endif
                </div>
                <span class="text-sm font-semibold {{ $currentStep === 1 ? 'text-zinc-900 dark:text-zinc-100 font-bold' : 'text-zinc-400 dark:text-zinc-500' }}">{{ __('Demographics') }}</span>
            </div>

            <!-- Divider -->
            <div class="flex-1 h-0.5 mx-4 transition-colors {{ $currentStep > 1 ? 'bg-primary' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>

            <!-- Step 2: Contact & Emergency -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors {{ $currentStep >= 2 ? 'bg-primary text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500' }}">
                    @if ($currentStep > 2)
                        <flux:icon.check class="size-4" />
                    @else
                        2
                    @endif
                </div>
                <span class="text-sm font-semibold {{ $currentStep === 2 ? 'text-zinc-900 dark:text-zinc-100 font-bold' : 'text-zinc-400 dark:text-zinc-500' }}">{{ __('Contact & Emergency') }}</span>
            </div>

            <!-- Divider -->
            <div class="flex-1 h-0.5 mx-4 transition-colors {{ $currentStep > 2 ? 'bg-primary' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>

            <!-- Step 3: Insurance & Clinical -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors {{ $currentStep === 3 ? 'bg-primary text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500' }}">
                    3
                </div>
                <span class="text-sm font-semibold {{ $currentStep === 3 ? 'text-zinc-900 dark:text-zinc-100 font-bold' : 'text-zinc-400 dark:text-zinc-500' }}">{{ __('Insurance & Clinical') }}</span>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="max-w-2xl space-y-6" enctype="multipart/form-data">
        <!-- STEP 1: DEMOGRAPHICS -->
        @if ($currentStep === 1)
            <div class="space-y-6">
                <flux:subheading>{{ __('Demographics') }}</flux:subheading>

                <!-- Patient Photo Upload Section -->
                <div class="flex items-center gap-6 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/40">
                    <div class="relative shrink-0">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="" class="h-20 w-20 rounded-full object-cover border-2 border-primary shadow-md" />
                        @elseif ($patient && $patient->photo_path)
                            <img src="{{ Storage::url($patient->photo_path) }}" alt="" class="h-20 w-20 rounded-full object-cover border border-zinc-200 dark:border-zinc-700 shadow-sm" />
                        @else
                            <div class="h-20 w-20 rounded-full bg-zinc-200 dark:bg-zinc-805 border border-dashed border-zinc-300 dark:border-zinc-700 flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500">
                                <flux:icon.user class="size-8" />
                            </div>
                        @endif
                    </div>
                    
                    <div class="space-y-1.5 flex-1">
                        <flux:label for="photo_file">{{ __('Patient Photo') }}</flux:label>
                        <input 
                            type="file" 
                            id="photo_file"
                            wire:model="photo" 
                            class="block w-full text-xs text-zinc-500 dark:text-zinc-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer file:cursor-pointer"
                            accept="image/*"
                        />
                        <flux:error name="photo" />
                        <flux:text class="text-[10px] text-zinc-400 dark:text-zinc-500">{{ __('JPG, PNG, or WebP. Max 1MB.') }}</flux:text>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="first_name" :label="__('First Name')" required />
                    <flux:input wire:model="last_name" :label="__('Last Name')" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="mrn" :label="__('MRN')" required />
                    <flux:input wire:model="date_of_birth" type="date" :label="__('Date of Birth')" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="gender_identity" :label="__('Gender Identity')" />
                    <flux:input wire:model="pronouns" :label="__('Pronouns')" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="race_ethnicity" :label="__('Race / Ethnicity')" />
                    <flux:select wire:model="preferred_language" :label="__('Preferred Language')">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="zh">Chinese</option>
                        <option value="ar">Arabic</option>
                    </flux:select>
                </div>
            </div>
        @endif

        <!-- STEP 2: CONTACT & EMERGENCY -->
        @if ($currentStep === 2)
            <div class="space-y-6">
                <flux:subheading>{{ __('Contact Information') }}</flux:subheading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="phone" type="tel" :label="__('Phone')" />
                    <flux:input wire:model="email" type="email" :label="__('Email')" />
                </div>

                <flux:textarea wire:model="address" :label="__('Address')" />

                <flux:separator />

                <flux:subheading>{{ __('Emergency Contact') }}</flux:subheading>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="emergency_contact_name" :label="__('Name')" />
                    <flux:input wire:model="emergency_contact_phone" type="tel" :label="__('Phone')" />
                </div>

                <flux:input wire:model="emergency_contact_relationship" :label="__('Relationship')" />
            </div>
        @endif

        <!-- STEP 3: INSURANCE & CLINICAL -->
        @if ($currentStep === 3)
            <div class="space-y-6">
                <flux:subheading>{{ __('Insurance Details') }}</flux:subheading>

                <flux:input wire:model="primary_insurance" :label="__('Primary Insurance')" />
                <flux:input wire:model="secondary_insurance" :label="__('Secondary Insurance')" />

                <flux:separator />

                <flux:subheading>{{ __('Clinical Information') }}</flux:subheading>
                <flux:textarea wire:model="problem_list" :label="__('Problem List')" rows="3" />
                <flux:textarea wire:model="allergies" :label="__('Allergies')" rows="3" />
                
                <flux:separator />
                
                <flux:subheading>{{ __('Medical History') }}</flux:subheading>
                <flux:textarea wire:model="past_medical_history" :label="__('Past Medical History')" rows="3" />
                <flux:textarea wire:model="surgical_history" :label="__('Surgical History')" rows="3" />
                <flux:textarea wire:model="family_history" :label="__('Family History')" rows="3" />
                <flux:textarea wire:model="social_history" :label="__('Social History')" rows="3" />
            </div>
        @endif

        <!-- Form Navigation Controls -->
        <div class="flex items-center justify-between pt-6 border-t border-zinc-200 dark:border-zinc-800">
            <div>
                @if ($currentStep > 1)
                    <flux:button type="button" variant="outline" wire:click="previousStep">
                        {{ __('Back') }}
                    </flux:button>
                @else
                    <flux:button variant="ghost" href="{{ route('patients.index') }}" wire:navigate>
                        {{ __('Cancel') }}
                    </flux:button>
                @endif
            </div>

            <div>
                @if ($currentStep < 3)
                    <flux:button type="button" variant="primary" wire:click="nextStep">
                        {{ __('Next') }}
                    </flux:button>
                @else
                    <flux:button variant="primary" type="submit">
                        {{ $editing ? __('Update Patient') : __('Save Changes') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </form>
</flux:main>
