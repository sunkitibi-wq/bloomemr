<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                @php($user = auth()->user())

                @if ($user && $user->role === 'pharmacist')
                    <flux:sidebar.group :heading="__('Pharmacy')" class="grid">
                        <flux:sidebar.item icon="beaker" :href="route('pharmacy.portal')" :current="request()->routeIs('pharmacy.portal')" wire:navigate>
                            {{ __('Prescription Queue') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="users" :href="route('patients.index')" :current="request()->routeIs('patients.*')" wire:navigate>
                            {{ __('Patient Lookup') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="archive-box" :href="route('inventory')" :current="request()->routeIs('inventory')" wire:navigate>
                            {{ __('Drug Inventory') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="envelope" :href="route('messages')" :current="request()->routeIs('messages')" wire:navigate>
                            {{ __('Messages') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>

                    <flux:sidebar.group :heading="__('Configuration')" class="grid">
                        <flux:sidebar.item icon="building-storefront" :href="route('settings.pharmacies')" :current="request()->routeIs('settings.pharmacies')" wire:navigate>
                            {{ __('Pharmacies') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="cog" :href="route('profile.edit')" :current="request()->routeIs('profile.*') || request()->routeIs('security.*') || request()->routeIs('appearance.*')" wire:navigate>
                            {{ __('Settings') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @else
                    <flux:sidebar.group :heading="__('Clinical')" class="grid">
                        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>

                        @if ($user && $user->can('view_patients'))
                            <flux:sidebar.item icon="users" :href="route('patients.index')" :current="request()->routeIs('patients.*')" wire:navigate>
                                {{ __('Patients') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($user && $user->role !== 'guardian' && $user->role !== 'super_admin' && $user->role !== 'clinical_staff')
                            <flux:sidebar.item icon="user-group" :href="route('portal.dashboard')" :current="request()->routeIs('portal.dashboard')" wire:navigate>
                                {{ __('Patient Portal') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($user && in_array($user->role, ['attending', 'super_admin', 'resident', 'clinical_staff'], true))
                            <flux:sidebar.item icon="calendar" :href="route('scheduling')" :current="request()->routeIs('scheduling')" wire:navigate>
                                {{ __('Scheduling') }}
                            </flux:sidebar.item>
                            
                            <flux:sidebar.item icon="queue-list" :href="route('scheduling.flow-board')" :current="request()->routeIs('scheduling.flow-board')" wire:navigate>
                                {{ __('Flow Board') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($user && in_array($user->role, ['attending', 'super_admin', 'billing_admin'], true))
                            <flux:sidebar.item icon="credit-card" :href="route('billing')" :current="request()->routeIs('billing')" wire:navigate>
                                {{ __('Billing') }}
                            </flux:sidebar.item>
                            
                            <flux:sidebar.item icon="document-text" :href="route('billing.claims')" :current="request()->routeIs('billing.claims')" wire:navigate>
                                {{ __('Claims Center') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($user && in_array($user->role, ['attending', 'super_admin'], true))
                            <flux:sidebar.item icon="beaker" :href="route('settings.pharmacies')" :current="request()->routeIs('settings.pharmacies')" wire:navigate>
                                {{ __('Pharmacies') }}
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>

                    <flux:sidebar.group :heading="__('Operations')" class="grid">
                        <flux:sidebar.item icon="envelope" :href="route('messages')" :current="request()->routeIs('messages')" wire:navigate>
                            {{ __('Messages') }}
                        </flux:sidebar.item>

                        @if ($user && in_array($user->role, ['attending', 'super_admin', 'clinical_staff'], true))
                            <flux:sidebar.item icon="archive-box" :href="route('inventory')" :current="request()->routeIs('inventory')" wire:navigate>
                                {{ __('Inventory') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($user && in_array($user->role, ['attending', 'super_admin'], true))
                            <flux:sidebar.item icon="user-group" :href="route('population-health')" :current="request()->routeIs('population-health')" wire:navigate>
                                {{ __('Population Health') }}
                            </flux:sidebar.item>

                            <flux:sidebar.item icon="chart-bar" :href="route('analytics')" :current="request()->routeIs('analytics')" wire:navigate>
                                {{ __('Analytics') }}
                            </flux:sidebar.item>
                            
                            <flux:sidebar.item icon="clipboard-document-check" :href="route('analytics.cqm')" :current="request()->routeIs('analytics.cqm')" wire:navigate>
                                {{ __('Quality Measures') }}
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>

                    @if ($user && $user->isSystemAdmin())
                        <flux:sidebar.group :heading="__('System Admin')" class="grid">
                            <flux:sidebar.item icon="building-office" :href="route('system.practices')" :current="request()->routeIs('system.practices')" wire:navigate>
                                {{ __('All Practices') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="check-badge" :href="route('system.kyc')" :current="request()->routeIs('system.kyc')" wire:navigate>
                                {{ __('KYC Verifications') }}
                            </flux:sidebar.item>
                        </flux:sidebar.group>
                    @endif

                    @if ($user && ! $user->isSystemAdmin() && $user->role !== 'guardian')
                        <flux:sidebar.group :heading="__('License Plan')" class="grid">
                            <flux:sidebar.item icon="credit-card" :href="route('subscribe')" :current="request()->routeIs('subscribe')" wire:navigate>
                                @if ($user->subscribed_until)
                                    {{ __('Active until :date', ['date' => $user->subscribed_until->format('M j, Y')]) }}
                                @else
                                    {{ __('Enterprise License') }}
                                @endif
                            </flux:sidebar.item>
                        </flux:sidebar.group>
                    @endif

                    <flux:sidebar.group :heading="__('Configuration')" class="grid">
                        @if ($user && $user->can('manage_users'))
                            <flux:sidebar.item icon="book-open-text" :href="route('smart-phrases.edit')" :current="request()->routeIs('smart-phrases.*')" wire:navigate>
                                {{ __('Smart Phrases') }}
                            </flux:sidebar.item>
                        @endif

                        <flux:sidebar.item icon="cog" :href="route('profile.edit')" :current="request()->routeIs('profile.*') || request()->routeIs('security.*') || request()->routeIs('appearance.*') || request()->routeIs('settings.users')" wire:navigate>
                            {{ __('Settings') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
