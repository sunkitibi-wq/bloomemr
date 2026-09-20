<aside class="flex-none flex flex-col gap-6 transition-all duration-300 overflow-hidden hidden xl:flex" :class="rightCollapsed ? 'w-0 opacity-0' : 'w-[320px] opacity-100'">
    
    <!-- AI Assistant Panel -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800 dark:text-slate-200 flex items-center gap-2">
                AI Assistant <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-widest">Beta</span>
            </h3>
            <button class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <flux:icon.chevron-up class="w-4 h-4" />
            </button>
        </div>

        <div class="mb-4">
            <p class="text-[13px] text-slate-600 dark:text-slate-400 mb-1">Hello Olasunkanmi,</p>
            <p class="text-[13px] text-slate-600 dark:text-slate-400 font-medium">How can I help you today?</p>
        </div>

        <div class="flex flex-col gap-2 mb-4">
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/50 transition-colors group">
                <flux:icon.sparkles class="w-4 h-4 text-indigo-500 dark:text-indigo-400" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 dark:text-slate-300 group-hover:text-indigo-700 dark:group-hover:text-indigo-400">Summarize today's activities</span>
            </button>
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/50 transition-colors group">
                <flux:icon.document-text class="w-4 h-4 text-indigo-500 dark:text-indigo-400" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 dark:text-slate-300 group-hover:text-indigo-700 dark:group-hover:text-indigo-400">Show pending tasks</span>
            </button>
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/50 transition-colors group">
                <flux:icon.chart-bar-square class="w-4 h-4 text-indigo-500 dark:text-indigo-400" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 dark:text-slate-300 group-hover:text-indigo-700 dark:group-hover:text-indigo-400">Generate report</span>
            </button>
        </div>

        <div class="relative">
            <input type="text" class="block w-full pl-4 pr-12 py-3 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-800 placeholder-slate-400 dark:placeholder-slate-500 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] transition-colors" placeholder="Ask anything..." />
            <button class="absolute inset-y-1.5 right-1.5 bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-400 text-white rounded-lg p-1.5 transition-colors flex items-center justify-center">
                <flux:icon.paper-airplane class="w-4 h-4" variant="solid" />
            </button>
        </div>
    </div>

    <!-- Alerts Panel -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800 dark:text-slate-200">Alerts</h3>
            <a href="{{ route('dashboard') }}" wire:navigate class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 uppercase tracking-widest">View all</a>
        </div>
        
        @php
            $pendingAppointmentsCount = \App\Models\Appointment::where('status', 'scheduled')->whereDate('appointment_date', '>=', today())->count();
            $unpaidInvoicesCount = \App\Models\Invoice::whereIn('status', ['draft', 'pending'])->count();
        @endphp

        <div class="flex flex-col gap-1">
            <a href="{{ route('scheduling') }}" wire:navigate class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group">
                <div class="mt-0.5 text-indigo-500 dark:text-indigo-400"><flux:icon.calendar class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 dark:text-slate-300 font-medium leading-snug">{{ $pendingAppointmentsCount }} appointments pending</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 dark:group-hover:text-slate-400 transition-colors shrink-0" />
            </a>
            <hr class="border-slate-100 dark:border-slate-800 my-1" />
            <a href="{{ route('billing') }}" wire:navigate class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group">
                <div class="mt-0.5 text-amber-500 dark:text-amber-400"><flux:icon.currency-dollar class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 dark:text-slate-300 font-medium leading-snug">{{ $unpaidInvoicesCount }} unpaid invoices</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 dark:group-hover:text-slate-400 transition-colors shrink-0" />
            </a>
        </div>
    </div>

    <!-- Quick Notes Panel -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800 dark:text-slate-200">Quick Notes</h3>
            <div class="flex items-center gap-3">
                <a href="/" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 uppercase tracking-widest">View all</a>
                <button class="p-1 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                    <flux:icon.plus class="w-4 h-4" />
                </button>
            </div>
        </div>

        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-100/50 dark:border-amber-900/50">
            <h4 class="text-[13px] font-bold text-slate-800 dark:text-slate-200 mb-1">Team meeting tomorrow at 10AM</h4>
            <p class="text-[13px] text-slate-600 dark:text-slate-400 mb-4 leading-relaxed">Discuss new inventory module</p>
            <div class="text-[11px] font-medium text-slate-400 dark:text-slate-500 flex items-center gap-1">
                May 20, 2025 &bull; Olasunkanmi
            </div>
        </div>
    </div>
    
</aside>
