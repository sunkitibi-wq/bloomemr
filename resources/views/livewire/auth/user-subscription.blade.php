<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 py-12 px-4 sm:px-6 lg:px-8 flex flex-col justify-between">
    <div class="max-w-4xl w-full mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Individual Plans -->
        <div class="col-span-12 md:col-span-7 bg-white dark:bg-zinc-900 shadow-xl rounded-3xl p-8 border border-slate-100 dark:border-zinc-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-growth-sage flex items-center justify-center text-white font-bold">
                    $
                </div>
                <div>
                    <h2 class="text-xl font-bold text-trust-navy dark:text-zinc-150">{{ __('Individual Practitioner License') }}</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">{{ __('Select a subscription duration for your clinical workspace') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-6">
                <!-- 1 Month -->
                <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col items-center justify-between text-center transition-all {{ $selectedPlan === '1_month' ? 'border-growth-sage bg-growth-sage/5 dark:bg-growth-sage/10' : 'border-slate-100 dark:border-zinc-800 hover:border-slate-200' }}">
                    <input type="radio" wire:model.live="selectedPlan" value="1_month" class="sr-only" />
                    <span class="text-xs font-bold text-zinc-500 uppercase">{{ __('1 Month') }}</span>
                    <span class="text-2xl font-bold text-trust-navy dark:text-zinc-150 mt-2">$99</span>
                    <span class="text-[10px] text-zinc-400 mt-1">{{ __('Billed monthly') }}</span>
                </label>

                <!-- 6 Months -->
                <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col items-center justify-between text-center transition-all relative {{ $selectedPlan === '6_months' ? 'border-growth-sage bg-growth-sage/5 dark:bg-growth-sage/10' : 'border-slate-100 dark:border-zinc-800 hover:border-slate-200' }}">
                    <input type="radio" wire:model.live="selectedPlan" value="6_months" class="sr-only" />
                    <span class="absolute -top-2.5 bg-growth-sage text-white text-[8px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">{{ __('Save 15%') }}</span>
                    <span class="text-xs font-bold text-zinc-500 uppercase mt-1">{{ __('6 Months') }}</span>
                    <span class="text-2xl font-bold text-trust-navy dark:text-zinc-150 mt-2">$499</span>
                    <span class="text-[10px] text-zinc-400 mt-1">{{ __('Billed upfront') }}</span>
                </label>

                <!-- 1 Year -->
                <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col items-center justify-between text-center transition-all relative {{ $selectedPlan === '1_year' ? 'border-growth-sage bg-growth-sage/5 dark:bg-growth-sage/10' : 'border-slate-100 dark:border-zinc-800 hover:border-slate-200' }}">
                    <input type="radio" wire:model.live="selectedPlan" value="1_year" class="sr-only" />
                    <span class="absolute -top-2.5 bg-primary text-white text-[8px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">{{ __('Save 25%') }}</span>
                    <span class="text-xs font-bold text-zinc-500 uppercase mt-1">{{ __('1 Year') }}</span>
                    <span class="text-2xl font-bold text-trust-navy dark:text-zinc-150 mt-2">$899</span>
                    <span class="text-[10px] text-zinc-400 mt-1">{{ __('Billed upfront') }}</span>
                </label>
            </div>

            <!-- Stripe Credit Card Form -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-zinc-800">
                <h3 class="text-xs font-bold text-zinc-405 uppercase tracking-wider mb-2">{{ __('Payment details (Stripe Mock)') }}</h3>
                
                <flux:input wire:model="cardNumber" label="{{ __('Card Number') }}" placeholder="4000 1234 5678 9010" required />
                
                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="cardExpiry" label="{{ __('Expiration (MM/YY)') }}" placeholder="12/28" required />
                    <flux:input wire:model="cardCvc" label="{{ __('CVC') }}" placeholder="123" required />
                </div>

                <div class="pt-6">
                    <flux:button variant="primary" class="w-full h-12 bg-growth-sage hover:bg-growth-sage/90 font-bold" wire:click="processPayment">
                        {{ __('Activate Subscription') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Right Column: Enterprise/Clinic Signups -->
        <div class="col-span-12 md:col-span-5 bg-gradient-to-br from-trust-navy to-slate-900 shadow-xl rounded-3xl p-8 text-white">
            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-4xl text-growth-sage">domain</span>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ __('Clinics & Organizations') }}</h2>
                    <p class="text-xs text-slate-300 mt-0.5">{{ __('Enterprise integration & volume licensing') }}</p>
                </div>
            </div>

            <p class="text-xs text-slate-305 leading-relaxed mb-6">
                {{ __('Organizations cannot subscribe online. Bloom offers custom service level agreements (SLAs), dedicated servers, HIE connections, and staff roster management under a unified organization account. Contact support to finalize setup.') }}
            </p>

            @if ($isOrgFormSubmitted)
                <div class="bg-growth-sage/20 border border-growth-sage/40 rounded-2xl p-4 text-center">
                    <span class="material-symbols-outlined text-3xl text-growth-sage mb-2">mark_email_read</span>
                    <h4 class="text-sm font-bold text-white">{{ __('Request Submitted') }}</h4>
                    <p class="text-xs text-slate-200 mt-1">{{ __('Our enterprise onboarding team will contact you at your clinic email within 1 business hour.') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    <flux:input wire:model="orgName" label="{{ __('Clinic/Practice Name') }}" class="dark" placeholder="e.g. Metro Pediatrics" required />
                    <flux:input wire:model="orgEmail" label="{{ __('Billing/Admin Email') }}" class="dark" type="email" placeholder="e.g. admin@metro.com" required />
                    <flux:input wire:model="orgPhone" label="{{ __('Clinic Phone') }}" class="dark" placeholder="e.g. (555) 019-2834" required />

                    <flux:select wire:model="orgSize" label="{{ __('Staff / Provider Roster Size') }}" class="dark">
                        <option value="5">{{ __('1 to 5 Clinicians') }}</option>
                        <option value="20">{{ __('6 to 20 Clinicians') }}</option>
                        <option value="50">{{ __('21 to 50 Clinicians') }}</option>
                        <option value="100">{{ __('More than 50 Clinicians') }}</option>
                    </flux:select>

                    <div class="pt-4">
                        <flux:button variant="ghost" class="w-full text-white bg-white/10 hover:bg-white/20 border border-white/15" wire:click="submitOrgRequest">
                            {{ __('Contact Sales') }}
                        </flux:button>
                    </div>
                </div>
            @endif

            <div class="mt-8 pt-6 border-t border-white/10 flex justify-center">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:button variant="ghost" type="submit" class="w-full text-white hover:bg-white/5">
                        {{ __('Cancel and Log Out') }}
                    </flux:button>
                </form>
            </div>
        </div>
    </div>
</div>
