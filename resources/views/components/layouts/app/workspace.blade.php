<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="h-screen w-screen overflow-hidden bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 flex" x-data="{ leftCollapsed: false, rightCollapsed: false }">
        <!-- Left Sidebar (Main Nav) -->
        <x-workspace.left-panel />

        <!-- Right Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-slate-900 shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.05)] dark:shadow-none border-l border-t border-transparent dark:border-slate-800 rounded-tl-3xl relative z-10 overflow-hidden">
            <!-- Top Header & Secondary Nav -->
            <x-top-navigation />

            <!-- Main Workspace Area -->
            <div class="flex flex-1 overflow-hidden p-6 gap-6 bg-slate-50/50 dark:bg-slate-900/50">
                <!-- Center Panel (Main Content) -->
                <main class="flex-1 min-w-0 overflow-y-auto flex flex-col gap-6">
                    {{ $slot }}
                </main>

                <!-- Right Panel (Auxiliary) -->
                <livewire:workspace.right-panel />
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
