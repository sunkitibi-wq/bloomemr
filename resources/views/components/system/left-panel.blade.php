<aside
    class="w-64 flex-shrink-0 flex flex-col bg-slate-900 text-slate-300 transition-all duration-300"
    :class="{ 'w-20': leftCollapsed }"
>
    <!-- Logo & Collapse Toggle -->
    <div class="h-16 flex items-center justify-between px-4 shrink-0 border-b border-slate-800">
        <div class="flex items-center gap-2" x-show="!leftCollapsed" x-transition.opacity>
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center shrink-0">
                <flux:icon.server class="w-5 h-5 text-white" />
            </div>
            <span class="font-bold text-lg text-white tracking-tight">System Admin</span>
        </div>
        <button @click="leftCollapsed = !leftCollapsed" class="p-1 hover:bg-slate-800 rounded-md transition-colors text-slate-400">
            <flux:icon.bars-3 class="w-5 h-5" />
        </button>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-6 px-3 flex flex-col gap-8 custom-scrollbar">
        <!-- Main Nav -->
        <nav class="space-y-1">
            <flux:navlist>
                <flux:navlist.item href="{{ route('system.dashboard') }}" wire:navigate icon="home" :current="request()->routeIs('system.dashboard')">
                    Dashboard
                </flux:navlist.item>
                <flux:navlist.item href="{{ route('system.practices') }}" wire:navigate icon="building-office-2" :current="request()->routeIs('system.practices')">
                    Tenants & Practices
                </flux:navlist.item>
                <flux:navlist.item href="{{ route('system.kyc') }}" wire:navigate icon="shield-check" :current="request()->routeIs('system.kyc')">
                    KYC Verification
                </flux:navlist.item>
            </flux:navlist>
        </nav>

        <div class="mt-auto">
            <flux:navlist>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:navlist.item href="#" onclick="event.preventDefault(); this.closest('form').submit();" icon="arrow-right-start-on-rectangle" class="text-red-400 hover:bg-red-500/10 hover:text-red-300">
                        Sign Out
                    </flux:navlist.item>
                </form>
            </flux:navlist>
        </div>
    </div>
</aside>
