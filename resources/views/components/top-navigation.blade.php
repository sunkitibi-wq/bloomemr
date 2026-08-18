<div class="flex-none bg-white rounded-tl-3xl z-20">
    <!-- Top Header -->
    <header class="h-20 flex items-center justify-between px-8 border-b border-slate-100">
        
        <!-- Left Section: Toggle & Global Search -->
        <div class="flex flex-1 max-w-2xl items-center">
            <!-- Toggle Left Panel -->
            <button @click="leftCollapsed = !leftCollapsed" class="p-2 mr-4 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors focus:outline-none shrink-0" title="Toggle Sidebar">
                <flux:icon.bars-3 class="h-6 w-6" />
            </button>

            <!-- Global Search -->
            <form action="{{ route('patients.index') }}" method="GET" class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon.magnifying-glass class="h-5 w-5 text-slate-400" />
                </div>
                <input type="text" name="search" class="block w-full pl-10 pr-16 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-[13px] transition-colors" placeholder="Search users, patients, products, help, tools, shared content..." />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-slate-400 sm:text-[11px] font-medium">Enter to search</span>
                </div>
            </form>
        </div>

        <!-- Right Section: Actions & Profile -->
        <div class="flex items-center gap-6 ml-4 shrink-0">
            
            <div class="flex items-center gap-4 border-r border-slate-200 pr-6">
                <!-- Notifications -->
                <livewire:notifications-dropdown />
                
                <!-- Tasks -->
                <a href="{{ route('tasks.index') }}" wire:navigate class="relative p-1 rounded-full text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                    <span class="sr-only">View tasks</span>
                    <flux:icon.clipboard-document-check class="h-6 w-6" variant="outline" />
                </a>

                <!-- Messages -->
                <a href="{{ route('messages') }}" wire:navigate class="relative p-1 rounded-full text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                    <span class="sr-only">View messages</span>
                    <flux:icon.chat-bubble-left-ellipsis class="h-6 w-6" variant="outline" />
                    <span class="absolute top-0 right-0 block h-3.5 w-3.5 rounded-full bg-red-500 ring-2 ring-white text-[9px] font-bold text-white flex items-center justify-center leading-none">3</span>
                </a>
            </div>

            <!-- User Profile Dropdown -->
            <flux:dropdown position="bottom" align="end">
                <button class="flex items-center gap-3 focus:outline-none">
                    @php
                        $user = auth()->user();
                        $name = $user ? $user->name : 'User';
                        $role = $user ? ucfirst(str_replace('_', ' ', $user->role)) : 'Staff';
                        $encodedName = urlencode($name);
                    @endphp
                    <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm" src="https://ui-avatars.com/api/?name={{ $encodedName }}&background=e0e7ff&color=4338ca" alt="{{ $name }}" />
                    <div class="hidden md:flex flex-col text-left">
                        <span class="text-[13px] font-bold text-slate-700 leading-tight">{{ $name }}</span>
                        <span class="text-[11px] font-medium text-slate-400 leading-tight">{{ $role }}</span>
                    </div>
                    <flux:icon.chevron-down class="h-4 w-4 text-slate-400 ml-1" />
                </button>
                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
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

            <!-- Toggle Right Panel -->
            <button @click="rightCollapsed = !rightCollapsed" class="p-2 ml-2 hidden xl:block text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors focus:outline-none shrink-0" title="Toggle Sidebar">
                <flux:icon.bars-3-bottom-right class="h-6 w-6" />
            </button>
        </div>
    </header>

    <!-- Secondary Nav -->
    <nav class="h-14 px-8 border-b border-slate-200 bg-white/50 backdrop-blur-sm">
        <ul class="flex items-center gap-8 h-full">
            <li class="h-full flex items-center border-b-2 border-indigo-600">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-bold text-indigo-700">
                    <flux:icon.home class="w-4 h-4" variant="outline" /> Dashboard
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 border-transparent hover:border-slate-300 transition-colors">
                <a href="{{ route('encounters.index') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-700">
                    <flux:icon.document-text class="w-4 h-4" variant="outline" /> Notes
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 border-transparent hover:border-slate-300 transition-colors">
                <a href="{{ route('patients.index') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-700">
                    <flux:icon.clipboard-document-list class="w-4 h-4" variant="outline" /> Records
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 border-transparent hover:border-slate-300 transition-colors">
                <a href="{{ route('patients.create') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-700">
                    <flux:icon.document-plus class="w-4 h-4" variant="outline" /> Intake / Registration
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 border-transparent hover:border-slate-300 transition-colors">
                <a href="{{ route('inventory') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-700">
                    <flux:icon.building-storefront class="w-4 h-4" variant="outline" /> Stores
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 border-transparent hover:border-slate-300 transition-colors">
                <a href="{{ route('billing') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-700">
                    <flux:icon.banknotes class="w-4 h-4" variant="outline" /> Finance
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 border-transparent hover:border-slate-300 transition-colors">
                <a href="{{ route('radiology') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-700">
                    <flux:icon.photo class="w-4 h-4" variant="outline" /> Media
                </a>
            </li>
            <li class="h-full flex items-center border-b-2 border-transparent hover:border-slate-300 transition-colors">
                <a href="{{ route('population-health') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-700">
                    <flux:icon.chart-bar class="w-4 h-4" variant="outline" /> Analytics
                </a>
            </li>
            <li class="h-full flex items-center ml-auto border-b-2 border-transparent hover:border-slate-300 transition-colors">
                <a href="{{ route('messages') }}" wire:navigate class="flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-700">
                    <flux:icon.face-smile class="w-4 h-4" variant="outline" /> Chat / Help
                </a>
            </li>
        </ul>
    </nav>
</div>
