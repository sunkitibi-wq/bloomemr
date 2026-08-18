<aside class="flex-none w-[320px] flex flex-col gap-6 hidden xl:flex">
    
    <!-- Health Assistant Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800 flex items-center gap-2">
                Health Assistant <span class="bg-indigo-100 text-indigo-700 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-widest">Beta</span>
            </h3>
            <button class="text-slate-400 hover:text-slate-600 transition-colors">
                <flux:icon.chevron-up class="w-4 h-4" />
            </button>
        </div>

        <div class="mb-4">
            <p class="text-[13px] text-slate-600 mb-1">Hello {{ auth('portal')->user()->name ?? auth()->user()->name }},</p>
            <p class="text-[13px] text-slate-600 font-medium">Have a health or billing question?</p>
        </div>

        <div class="flex flex-col gap-2 mb-4">
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                <flux:icon.heart class="w-4 h-4 text-indigo-500" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Check my symptoms</span>
            </button>
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                <flux:icon.banknotes class="w-4 h-4 text-indigo-500" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Understand my recent bill</span>
            </button>
            <button class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                <flux:icon.beaker class="w-4 h-4 text-indigo-500" variant="outline" />
                <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Request a medication refill</span>
            </button>
        </div>

        <div class="relative">
            <input type="text" class="block w-full pl-4 pr-12 py-3 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] transition-colors" placeholder="Type your question..." />
            <button class="absolute inset-y-1.5 right-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-1.5 transition-colors flex items-center justify-center">
                <flux:icon.paper-airplane class="w-4 h-4" variant="solid" />
            </button>
        </div>
    </div>

    <!-- Action Items Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800">To-Do Items</h3>
            <a href="/" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">View all</a>
        </div>
        
        <div class="flex flex-col gap-1">
            <a href="/" class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="mt-0.5 text-indigo-500"><flux:icon.document-text class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 font-medium leading-snug">Sign consent forms for upcoming visit</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors shrink-0" />
            </a>
            <hr class="border-slate-100 my-1" />
            <a href="/" class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="mt-0.5 text-amber-500"><flux:icon.banknotes class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 font-medium leading-snug">Pay outstanding $45.00 co-pay</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors shrink-0" />
            </a>
            <hr class="border-slate-100 my-1" />
            <a href="/" class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="mt-0.5 text-emerald-500"><flux:icon.calendar class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 font-medium leading-snug">Schedule annual physical exam</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors shrink-0" />
            </a>
        </div>
    </div>

    <!-- Quick Contacts Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800">Quick Contacts</h3>
            <div class="flex items-center gap-3">
                <a href="{{ route('portal.messages') }}" wire:navigate class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">Message</a>
            </div>
        </div>

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                    BC
                </div>
                <div>
                    <h4 class="text-[13px] font-bold text-slate-800 leading-tight">Bloom Clinic</h4>
                    <p class="text-[11px] text-slate-500">Primary Care Provider</p>
                </div>
            </div>
            <div class="flex flex-col gap-2 text-[12px] text-slate-600 font-medium">
                <div class="flex items-center gap-2">
                    <flux:icon.phone class="w-3.5 h-3.5 text-slate-400" /> (555) 123-4567
                </div>
                <div class="flex items-center gap-2">
                    <flux:icon.map-pin class="w-3.5 h-3.5 text-slate-400" /> 123 Health Ave, Suite 100
                </div>
            </div>
        </div>
    </div>
    
</aside>
