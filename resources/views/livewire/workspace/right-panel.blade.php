<aside class="flex-none flex flex-col gap-6 transition-all duration-300 overflow-hidden hidden xl:flex" :class="rightCollapsed ? 'w-0 opacity-0' : 'w-[320px] opacity-100'">
    
    <!-- AI Assistant Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col h-[400px]">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800 flex items-center gap-2">
                AI Assistant <span class="bg-emerald-100 text-emerald-700 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-widest">Beta</span>
            </h3>
            <button class="text-slate-400 hover:text-slate-600 transition-colors">
                <flux:icon.chevron-up class="w-4 h-4" />
            </button>
        </div>

        <!-- Chat messages area -->
        <div class="flex-1 overflow-y-auto mb-4 flex flex-col gap-3 pr-2 scrollbar-thin scrollbar-thumb-slate-200">
            @foreach ($chatMessages as $msg)
                <div class="flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">
                    <div class="text-[13px] {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }} rounded-xl px-3 py-2 max-w-[85%]">
                        {{ $msg['content'] }}
                    </div>
                </div>
            @endforeach
        </div>

        @if (count($chatMessages) <= 1)
            <!-- Suggested Actions (only show if no chat history beyond welcome) -->
            <div class="flex flex-col gap-2 mb-4 shrink-0">
                <button wire:click="triggerSuggestedAction('Summarize today\'s activities')" class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                    <flux:icon.sparkles class="w-4 h-4 text-indigo-500" variant="outline" />
                    <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Summarize today's activities</span>
                </button>
                <button wire:click="triggerSuggestedAction('Show pending tasks')" class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                    <flux:icon.document-text class="w-4 h-4 text-indigo-500" variant="outline" />
                    <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Show pending tasks</span>
                </button>
                <button wire:click="triggerSuggestedAction('Generate report')" class="flex items-center gap-3 w-full text-left p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                    <flux:icon.chart-bar-square class="w-4 h-4 text-indigo-500" variant="outline" />
                    <span class="text-[13px] font-medium text-slate-700 group-hover:text-indigo-700">Generate report</span>
                </button>
            </div>
        @endif

        <div class="relative shrink-0">
            <form wire:submit.prevent="sendMessage">
                <input wire:model="chatInput" type="text" class="block w-full pl-4 pr-12 py-3 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] transition-colors" placeholder="Ask anything..." />
                <button type="submit" class="absolute inset-y-1.5 right-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-1.5 transition-colors flex items-center justify-center">
                    <flux:icon.paper-airplane class="w-4 h-4" variant="solid" />
                </button>
            </form>
        </div>
    </div>

    <!-- Alerts Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800">Alerts</h3>
            <a href="{{ route('dashboard') }}" wire:navigate class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">View all</a>
        </div>
        
        <div class="flex flex-col gap-1">
            <a href="{{ route('scheduling') }}" wire:navigate class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="mt-0.5 text-indigo-500"><flux:icon.calendar class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 font-medium leading-snug">{{ $this->pendingAppointmentsCount }} appointments pending</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors shrink-0" />
            </a>
            <hr class="border-slate-100 my-1" />
            <a href="{{ route('billing') }}" wire:navigate class="flex items-start gap-3 p-2.5 -mx-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="mt-0.5 text-amber-500"><flux:icon.currency-dollar class="w-4 h-4" variant="outline" /></div>
                <div class="flex-1 text-[13px] text-slate-700 font-medium leading-snug">{{ $this->unpaidInvoicesCount }} unpaid invoices</div>
                <flux:icon.chevron-right class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors shrink-0" />
            </a>
        </div>
    </div>

    <!-- Quick Notes Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[14px] text-slate-800">Quick Notes</h3>
            <div class="flex items-center gap-3">
                <button wire:click="$toggle('isAddingNote')" class="p-1 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-md transition-colors">
                    <flux:icon.plus class="w-4 h-4" />
                </button>
            </div>
        </div>

        @if($isAddingNote)
            <form wire:submit.prevent="saveNote" class="mb-4 bg-slate-50 rounded-xl p-4 border border-slate-200">
                <div class="mb-3">
                    <input wire:model="noteTitle" type="text" placeholder="Note Title..." class="w-full text-[13px] font-bold text-slate-800 bg-transparent border-0 border-b border-slate-300 focus:ring-0 px-0 py-1" />
                    @error('noteTitle') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <textarea wire:model="noteBody" rows="2" placeholder="Note Body..." class="w-full text-[13px] text-slate-600 bg-transparent border-0 border-b border-slate-300 focus:ring-0 px-0 py-1 resize-none"></textarea>
                    @error('noteBody') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('isAddingNote', false)" class="text-[12px] font-medium text-slate-500 hover:text-slate-700">Cancel</button>
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-[12px] font-bold hover:bg-indigo-700">Save</button>
                </div>
            </form>
        @endif

        <div class="flex flex-col gap-3">
            @forelse($this->notes as $note)
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100/50 group relative">
                    <button wire:click="deleteNote({{ $note->id }})" class="absolute top-2 right-2 text-amber-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.trash class="w-3.5 h-3.5" />
                    </button>
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1 pr-6">{{ $note->title }}</h4>
                    <p class="text-[13px] text-slate-600 mb-4 leading-relaxed">{{ $note->body }}</p>
                    <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1">
                        {{ $note->created_at->format('M d, Y') }} &bull; {{ explode(' ', $note->user->name)[0] ?? 'User' }}
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-[12px] text-slate-400 italic">No quick notes found.</p>
                </div>
            @endforelse
        </div>
    </div>
    
</aside>
