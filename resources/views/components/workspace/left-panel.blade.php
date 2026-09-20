<aside class="flex-none bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-all duration-300 overflow-hidden" :class="leftCollapsed ? 'w-0 border-r-0 opacity-0' : 'w-[260px] opacity-100'">
    <!-- Branding -->
    <div class="h-20 flex items-center px-6 shrink-0 mt-2">
        <x-app-logo class="h-10" />
       
    </div>

    <!-- Scrollable Nav -->
    <div class="flex-1 overflow-y-auto py-4 px-4 flex flex-col gap-8 custom-scrollbar">
        
        <!-- Quick Access -->
        <div>
            <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3 px-2">Quick Access</div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') ?? '#' }}" wire:navigate class="flex items-center justify-between px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <flux:icon.home variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Home Page
                    </div>
                </a>
                <a href="#" class="flex items-center justify-between px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <flux:icon.magnifying-glass variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Patient Search
                    </div>
                </a>
                <a href="{{ route('patients.index') ?? '#' }}" wire:navigate class="flex items-center justify-between px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <flux:icon.users variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> My Patients
                    </div>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                    <flux:icon.building-office-2 variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Patient Facility or Unit
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                    <flux:icon.user-circle variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Patient Profile
                </a>
            </nav>
        </div>

        <!-- Facility & Department Context Selector -->
        <livewire:workspace.context-selector />

        <!-- Main Nav -->
        <nav class="space-y-1 mb-8">
            <a href="#" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                <flux:icon.document-chart-bar variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Reports
            </a>
            <a href="#" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                <flux:icon.clipboard-document-check variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Assessments
            </a>
            <a href="#" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                <flux:icon.document-text variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Forms
            </a>
            <a href="#" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                <flux:icon.wrench variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Tools
            </a>
            <a href="{{ route('settings.users') ?? '#' }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                <flux:icon.users variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Admin
            </a>
            <a href="{{ route('help.center') ?? '#' }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm dark:hover:shadow-none transition-all duration-200">
                <flux:icon.question-mark-circle variant="outline" class="w-[18px] h-[18px] text-slate-400 dark:text-slate-500" /> Help
            </a>
        </nav>

    </div>

    <!-- Footer -->
    <div class="p-4 shrink-0 mb-4">
        <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="w-7 h-7 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                <flux:icon.check-circle variant="solid" class="w-4 h-4 text-emerald-500 dark:text-emerald-400" />
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-700 dark:text-slate-200">System Status</div>
                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">All systems operational</div>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-between text-[11px] font-medium text-slate-400 dark:text-slate-500 px-1">
            <span class="flex items-center gap-4"><a href="/" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Help Center</a> <a href="/" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Privacy Policy</a> <a href="/" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Terms of Service</a></span>
        </div>
        <div class="mt-4 flex items-center gap-4 text-[11px] font-medium text-slate-400 dark:text-slate-500 px-1">
            <span>Gamut C ERP &copy; {{ date('Y')}}</span>
            <span>v1.0.0</span>
        </div>
    </div>
</aside>
