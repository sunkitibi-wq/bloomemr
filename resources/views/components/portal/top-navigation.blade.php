<div class="flex-none bg-white dark:bg-slate-900 rounded-tl-3xl z-20">
    <!-- Top Header -->
    <header class="h-20 flex items-center justify-between px-8 border-b border-slate-100 dark:border-slate-800">
        
        <div class="flex items-center gap-4 flex-1 max-w-3xl">
            <!-- Left Panel Toggle -->
            <button @click="leftCollapsed = !leftCollapsed" class="p-2 -ml-2 rounded-lg text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none shrink-0" title="Toggle Sidebar">
                <flux:icon.bars-3 class="h-5 w-5" variant="outline" />
            </button>
            
            <!-- Global Search -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon.magnifying-glass class="h-5 w-5 text-slate-400 dark:text-slate-500" />
                </div>
                <input type="text" class="block w-full pl-10 pr-16 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-800 placeholder-slate-400 dark:placeholder-slate-500 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-[13px] transition-colors" placeholder="Search your health records, lab results, upcoming visits..." />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-slate-400 dark:text-slate-500 sm:text-[11px] font-medium">Ctrl + K</span>
                </div>
            </div>
        </div>

        <!-- Right Section: Actions & Profile -->
        <div class="flex items-center gap-6 ml-4 shrink-0">
            
            <div class="flex items-center gap-4 border-r border-slate-200 dark:border-slate-700 pr-6">
                <!-- Notifications -->
                <flux:dropdown position="bottom" align="end">
                    <button class="relative p-1 rounded-full text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors focus:outline-none">
                        <span class="sr-only">View notifications</span>
                        <flux:icon.bell class="h-6 w-6" variant="outline" />
                        <span class="absolute top-0 right-0 block h-3.5 w-3.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-900 text-[9px] font-bold text-white flex items-center justify-center leading-none">2</span>
                    </button>
                    <flux:menu class="w-80">
                        <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="font-semibold text-sm">Notifications</h3>
                        </div>
                        <flux:menu.item>
                            <div class="flex flex-col">
                                <span class="font-medium">New test results</span>
                                <span class="text-xs text-slate-500">Your lab results from yesterday are ready.</span>
                            </div>
                        </flux:menu.item>
                        <flux:menu.item>
                            <div class="flex flex-col">
                                <span class="font-medium">Upcoming appointment reminder</span>
                                <span class="text-xs text-slate-500">Dr. Smith tomorrow at 2:00 PM.</span>
                            </div>
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <!-- Right Panel Toggle -->
                <button @click="rightCollapsed = !rightCollapsed" class="relative p-1 rounded-full text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors focus:outline-none" title="Toggle Right Panel">
                    <span class="sr-only">Toggle Right Panel</span>
                    <flux:icon.sparkles class="h-6 w-6" variant="outline" />
                </button>
            </div>

            <!-- User Profile Dropdown -->
            <flux:dropdown position="bottom" align="end">
                <button class="flex items-center gap-3 focus:outline-none">
                    @php
                        $user = auth('portal')->check() ? auth('portal')->user() : auth()->user();
                        $initials = strtoupper(substr($user->name, 0, 2));
                    @endphp
                    <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 font-bold flex items-center justify-center border-2 border-white dark:border-slate-800 shadow-sm">
                        {{ $initials }}
                    </div>
                    <div class="hidden md:flex flex-col text-left">
                        <span class="text-[13px] font-bold text-slate-700 dark:text-slate-200 leading-tight">{{ $user->name }}</span>
                        <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 leading-tight">Primary Guardian</span>
                    </div>
                    <flux:icon.chevron-down class="h-4 w-4 text-slate-400 dark:text-slate-500 ml-1" />
                </button>
                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Account Settings') }}
                    </flux:menu.item>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </header>

    <!-- Secondary Nav -->
    <nav class="h-14 px-8 border-b border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm">
        <ul class="flex items-center gap-8 h-full">
            <li class="h-full flex items-center border-b-2 {{ request()->routeIs('portal.dashboard') ? 'border-indigo-600 dark:border-indigo-500' : 'border-transparent hover:border-slate-300 dark:hover:border-slate-600' }} transition-colors">
                <a href="{{ route('portal.dashboard') }}" wire:navigate class="flex items-center gap-2 text-[13px] {{ request()->routeIs('portal.dashboard') ? 'font-bold text-indigo-700 dark:text-indigo-400' : 'font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <flux:icon.home class="w-4 h-4" variant="outline" /> Dashboard
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 {{ request()->routeIs('portal.education') ? 'border-indigo-600 dark:border-indigo-500' : 'border-transparent hover:border-slate-300 dark:hover:border-slate-600' }} transition-colors">
                <a href="{{ route('portal.education') }}" wire:navigate class="flex items-center gap-2 text-[13px] {{ request()->routeIs('portal.education') ? 'font-bold text-indigo-700 dark:text-indigo-400' : 'font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <flux:icon.academic-cap class="w-4 h-4" variant="outline" /> Educational Guides
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 {{ request()->routeIs('portal.documents') ? 'border-indigo-600 dark:border-indigo-500' : 'border-transparent hover:border-slate-300 dark:hover:border-slate-600' }} transition-colors">
                <a href="{{ route('portal.documents') }}" wire:navigate class="flex items-center gap-2 text-[13px] {{ request()->routeIs('portal.documents') ? 'font-bold text-indigo-700 dark:text-indigo-400' : 'font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <flux:icon.document-duplicate class="w-4 h-4" variant="outline" /> Documents & Forms
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 {{ request()->routeIs('portal.assessments') ? 'border-indigo-600 dark:border-indigo-500' : 'border-transparent hover:border-slate-300 dark:hover:border-slate-600' }} transition-colors">
                <a href="{{ route('portal.assessments') }}" wire:navigate class="flex items-center gap-2 text-[13px] {{ request()->routeIs('portal.assessments') ? 'font-bold text-indigo-700 dark:text-indigo-400' : 'font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <flux:icon.clipboard-document-check class="w-4 h-4" variant="outline" /> Assessments
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 {{ request()->routeIs('portal.visit-summaries') ? 'border-indigo-600 dark:border-indigo-500' : 'border-transparent hover:border-slate-300 dark:hover:border-slate-600' }} transition-colors">
                <a href="{{ route('portal.visit-summaries') }}" wire:navigate class="flex items-center gap-2 text-[13px] {{ request()->routeIs('portal.visit-summaries') ? 'font-bold text-indigo-700 dark:text-indigo-400' : 'font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <flux:icon.newspaper class="w-4 h-4" variant="outline" /> Visit Summaries
                </a>
            </li>
            
            <li class="h-full flex items-center ml-auto border-b-2 border-transparent hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                <a href="/" class="flex items-center gap-2 text-[13px] font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                    <flux:icon.face-smile class="w-4 h-4" variant="outline" /> Help & Support
                </a>
            </li>
        </ul>
    </nav>
</div>
