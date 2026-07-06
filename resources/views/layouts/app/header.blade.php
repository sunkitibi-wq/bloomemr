<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
                <flux:navbar.item icon="users" :href="route('patients.index')" :current="request()->routeIs('patients.*')" wire:navigate>
                    {{ __('Patients') }}
                </flux:navbar.item>
                <flux:navbar.item icon="calendar" :href="route('scheduling')" :current="request()->routeIs('scheduling')" wire:navigate>
                    {{ __('Scheduling') }}
                </flux:navbar.item>
                <flux:navbar.item icon="credit-card" :href="route('billing')" :current="request()->routeIs('billing')" wire:navigate>
                    {{ __('Billing') }}
                </flux:navbar.item>
                <flux:navbar.item icon="book-open-text" :href="route('smart-phrases.edit')" :current="request()->routeIs('smart-phrases.*')" wire:navigate>
                    {{ __('Smart Phrases') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip>
                <flux:tooltip :content="__('Repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        :label="__('Repository')"
                    />
                </flux:tooltip>
                <flux:tooltip :content="__('Documentation')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits#livewire"
                        target="_blank"
                        :label="__('Documentation')"
                    />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Clinical')">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('patients.index')" :current="request()->routeIs('patients.*')" wire:navigate>
                        {{ __('Patients')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('portal.dashboard')" :current="request()->routeIs('portal.dashboard')" wire:navigate>
                        {{ __('Patient Portal')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar" :href="route('scheduling')" :current="request()->routeIs('scheduling')" wire:navigate>
                        {{ __('Scheduling')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="credit-card" :href="route('billing')" :current="request()->routeIs('billing')" wire:navigate>
                        {{ __('Billing')  }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Configuration')">
                    <flux:sidebar.item icon="book-open-text" :href="route('smart-phrases.edit')" :current="request()->routeIs('smart-phrases.*')" wire:navigate>
                        {{ __('Smart Phrases')  }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="cog" :href="route('profile.edit')" :current="request()->routeIs('profile.*') || request()->routeIs('security.*') || request()->routeIs('appearance.*') || request()->routeIs('settings.users')" wire:navigate>
                        {{ __('Settings')  }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
