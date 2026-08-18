<aside class="flex-none flex flex-col gap-6 transition-all duration-300 overflow-hidden hidden xl:flex" :class="rightCollapsed ? 'w-0 opacity-0' : 'w-[320px] opacity-100'">
    
    <!-- AI Assistant Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800 flex items-center gap-2">
                AI Assistant <span class="bg-emerald-100 text-emerald-700 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-widest">Beta</span>
            </h3>
            <button class="text-slate-400 hover:text-slate-600 transition-colors">
                <flux:icon.chevron-up class="w-4 h-4" />
            </button>
        </div>

        <div class="mb-4">
            <p class="text-[13px] text-slate-600 mb-1">Hello Olasunkanmi,</p>
            <p class="text-[13px] text-slate-600 font-medium">How can I help you today?</p>
        </div>

        <div class="flex flex-col gap-2 mb-4">
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                <flux:icon.sparkles class="w-4 h-4 text-indigo-500" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Summarize today's activities</span>
            </button>
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                <flux:icon.document-text class="w-4 h-4 text-indigo-500" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Show pending tasks</span>
            </button>
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                <flux:icon.chart-bar-square class="w-4 h-4 text-indigo-500" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Generate report</span>
            </button>
        </div>

        <div class="relative">
            <input type="text" class="block w-full pl-4 pr-12 py-3 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] transition-colors" placeholder="Ask anything..." />
            <button class="absolute inset-y-1.5 right-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-1.5 transition-colors flex items-center justify-center">
                <flux:icon.paper-airplane class="w-4 h-4" variant="solid" />
            </button>
        </div>
    </div>

    <!-- Alerts Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800">Alerts</h3>
            <a href="{{ route('dashboard') }}" wire:navigate class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">View all</a>
        </div>
        
        @php
            $pendingAppointmentsCount = \App\Models\Appointment::where('status', 'scheduled')->whereDate('appointment_date', '>=', today())->count();
            $unpaidInvoicesCount = \App\Models\Invoice::whereIn('status', ['draft', 'pending'])->count();
        @endphp

        <div class="flex flex-col gap-1">
            <a href="{{ route('scheduling') }}" wire:navigate class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="mt-0.5 text-indigo-500"><flux:icon.calendar class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 font-medium leading-snug">{{ $pendingAppointmentsCount }} appointments pending</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors shrink-0" />
            </a>
            <hr class="border-slate-100 my-1" />
            <a href="{{ route('billing') }}" wire:navigate class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="mt-0.5 text-amber-500"><flux:icon.currency-dollar class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 font-medium leading-snug">{{ $unpaidInvoicesCount }} unpaid invoices</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors shrink-0" />
            </a>
        </div>
    </div>

    <!-- Quick Notes Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800">Quick Notes</h3>
            <div class="flex items-center gap-3">
                <a href="/" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">View all</a>
                <button class="p-1 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-md transition-colors">
                    <flux:icon.plus class="w-4 h-4" />
                </button>
            </div>
        </div>

        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100/50">
            <h4 class="text-[13px] font-bold text-slate-800 mb-1">Team meeting tomorrow at 10AM</h4>
            <p class="text-[13px] text-slate-600 mb-4 leading-relaxed">Discuss new inventory module</p>
            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1">
                May 20, 2025 &bull; Olasunkanmi
            </div>
        </div>
    </div>
    
</aside>
