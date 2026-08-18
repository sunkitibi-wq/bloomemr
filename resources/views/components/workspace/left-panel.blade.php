<aside class="flex-none bg-slate-50 border-r border-slate-200 flex flex-col transition-all duration-300 overflow-hidden" :class="leftCollapsed ? 'w-0 border-r-0 opacity-0' : 'w-[260px] opacity-100'">
    <!-- Branding -->
    <div class="h-20 flex items-center px-6 shrink-0 mt-2">
        <x-app-logo class="h-10" />
       
    </div>

    <!-- Scrollable Nav -->
    <div class="flex-1 overflow-y-auto py-4 px-4 flex flex-col gap-8 custom-scrollbar">
        
        <!-- Quick Access -->
        <div>
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3 px-2">Quick Access</div>
            <nav class="space-y-1">
                @php
                    $pendingTasksCount = \App\Models\Task::whereIn('status', ['pending', 'in_progress'])->count();
                @endphp
                <a href="{{ route('tasks.index') }}" wire:navigate class="flex items-center justify-between px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <flux:icon.check-circle variant="outline" class="w-[18px] h-[18px] text-slate-400" /> My Tasks
                    </div>
                    @if($pendingTasksCount > 0)
                        <span class="bg-indigo-100 text-indigo-700 py-0.5 px-2 rounded-full text-xs font-bold">{{ $pendingTasksCount }}</span>
                    @endif
                </a>
                <a href="{{ route('scheduling') }}" wire:navigate class="flex items-center justify-between px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <flux:icon.calendar variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Appointments
                    </div>
                    <span class="bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-xs font-bold">5</span>
                </a>
                @php
                    $unreadMessagesCount = \App\Models\SecureMessage::where('recipient_id', Auth::id())->whereNull('read_at')->count();
                @endphp
                <a href="{{ route('messages') }}" wire:navigate class="flex items-center justify-between px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <flux:icon.chat-bubble-left-ellipsis variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Messages
                    </div>
                    @if($unreadMessagesCount > 0)
                        <span class="bg-purple-100 text-purple-700 py-0.5 px-2 rounded-full text-xs font-bold">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>
                <a href="/" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                    <flux:icon.share variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Shared Content
                </a>
                <a href="/" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                    <flux:icon.question-mark-circle variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Help Center
                </a>
                <a href="/" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                    <flux:icon.wrench variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Tools
                </a>
            </nav>
        </div>

        <!-- Facility -->
        <div>
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3 px-2">Facility</div>
            <flux:select size="sm" class="w-full bg-white border-none shadow-sm rounded-lg" variant="default">
                <x-slot:icon>
                    <flux:icon.building-office-2 class="w-4 h-4 text-slate-400 ml-1" />
                </x-slot:icon>
                <flux:select.option>General Hospital</flux:select.option>
            </flux:select>
        </div>

        <!-- Department -->
        <div class="-mt-4">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3 px-2">Department</div>
            <flux:select size="sm" class="w-full bg-white border-none shadow-sm rounded-lg" variant="default">
                <x-slot:icon>
                    <flux:icon.building-office class="w-4 h-4 text-slate-400 ml-1" />
                </x-slot:icon>
                <flux:select.option>All Departments</flux:select.option>
            </flux:select>
        </div>

        <!-- Main Nav -->
        <nav class="space-y-1 mb-8">
            <a href="{{ route('patients.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-bold text-indigo-700 bg-indigo-50/50 rounded-lg relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 rounded-r-md"></div>
                <flux:icon.user-group variant="solid" class="w-[18px] h-[18px] text-indigo-600" /> Patients / Customers
            </a>
            <a href="{{ route('inventory') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <flux:icon.archive-box variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Products / Inventory
            </a>
            <a href="{{ route('vendors.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <flux:icon.truck variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Vendors
            </a>
            <a href="{{ route('telemedicine') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <flux:icon.video-camera variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Telemedicine
            </a>
            <a href="{{ route('settings.users') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <flux:icon.users variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Staff / Users
            </a>
            <a href="{{ route('population-health') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <flux:icon.document-chart-bar variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Reports
            </a>
            <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-600 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <flux:icon.cog-6-tooth variant="outline" class="w-[18px] h-[18px] text-slate-400" /> Settings
            </a>
        </nav>

    </div>

    <!-- Footer -->
    <div class="p-4 shrink-0 mb-4">
        <div class="flex items-center gap-3 p-3 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center">
                <flux:icon.check-circle variant="solid" class="w-4 h-4 text-emerald-500" />
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-700">System Status</div>
                <div class="text-[10px] text-slate-400 mt-0.5">All systems operational</div>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-between text-[11px] font-medium text-slate-400 px-1">
            <span class="flex items-center gap-4"><a href="/" class="hover:text-slate-600 transition-colors">Help Center</a> <a href="/" class="hover:text-slate-600 transition-colors">Privacy Policy</a> <a href="/" class="hover:text-slate-600 transition-colors">Terms of Service</a></span>
        </div>
        <div class="mt-4 flex items-center gap-4 text-[11px] font-medium text-slate-400 px-1">
            <span>Branner ERP &copy; 2025</span>
            <span>v1.0.0</span>
        </div>
    </div>
</aside>
