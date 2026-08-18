<div class="max-w-[1600px] w-full mx-auto py-8 h-full flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 px-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Message Center</h1>
            <p class="text-sm text-slate-500 mt-1">Secure intra-clinic messaging and patient communications</p>
        </div>
        <div class="flex items-center gap-3">
            <flux:button wire:click="openCompose" variant="primary" icon="pencil-square" class="rounded-xl shadow-sm">
                Compose
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-[calc(100vh-160px)] min-h-[500px]">
        
        <!-- Sidebar: Message List -->
        <div class="md:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden h-full">
            <div class="border-b border-slate-100 flex p-1">
                <button wire:click="$set('activeTab', 'inbox')" class="flex-1 py-2 text-sm font-semibold text-center rounded-xl transition-colors {{ $activeTab === 'inbox' ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                    Inbox
                </button>
                <button wire:click="$set('activeTab', 'sent')" class="flex-1 py-2 text-sm font-semibold text-center rounded-xl transition-colors {{ $activeTab === 'sent' ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                    Sent
                </button>
            </div>
            
            <div class="overflow-y-auto flex-1 custom-scrollbar">
                @forelse($messages as $msg)
                    <div wire:click="selectMessage({{ $msg->id }})" class="p-4 border-b border-slate-50 cursor-pointer hover:bg-slate-50 transition-colors relative {{ $selectedMessageId === $msg->id ? 'bg-indigo-50/50' : '' }}">
                        @if($selectedMessageId === $msg->id)
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 rounded-r-md"></div>
                        @endif
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm truncate pr-2 {{ null === $msg->read_at && $activeTab === 'inbox' ? 'font-bold text-slate-900' : 'font-semibold text-slate-700' }}">
                                {{ $activeTab === 'inbox' ? $msg->sender->name : $msg->recipient->name }}
                            </span>
                            <span class="text-[11px] font-medium text-slate-400 whitespace-nowrap">{{ $msg->created_at->format('M d') }}</span>
                        </div>
                        <div class="text-[13px] {{ null === $msg->read_at && $activeTab === 'inbox' ? 'text-slate-800 font-medium' : 'text-slate-500' }} truncate mb-1">
                            {{ $msg->subject }}
                        </div>
                        @if($msg->patient_id)
                            <div class="text-[11px] font-medium text-indigo-600 flex items-center gap-1 mt-2">
                                <flux:icon.user class="w-3.5 h-3.5" /> {{ $msg->patient->full_name }}
                            </div>
                        @endif
                        
                        @if(null === $msg->read_at && $activeTab === 'inbox')
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-indigo-500"></div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center flex flex-col items-center">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                            <flux:icon.inbox class="w-6 h-6 text-slate-400" variant="outline" />
                        </div>
                        <p class="text-sm font-semibold text-slate-700">No messages</p>
                        <p class="text-xs text-slate-500 mt-1">Your {{ $activeTab }} is empty.</p>
                    </div>
                @endforelse
            </div>
            @if($messages->hasPages())
                <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                    {{ $messages->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>

        <!-- Main Content: Message Detail -->
        <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 h-full flex flex-col overflow-hidden">
            @if($selectedMessage)
                <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">{{ $selectedMessage->subject }}</h2>
                    
                    <div class="flex justify-between items-start">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold shrink-0">
                                {{ substr($selectedMessage->sender->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm">
                                    <span class="font-bold text-slate-900">{{ $selectedMessage->sender->name }}</span>
                                    <span class="text-slate-500 mx-1">to</span> 
                                    <span class="font-medium text-slate-700">{{ $selectedMessage->recipient->name }}</span>
                                </div>
                                <div class="text-[11px] font-medium text-slate-400 mt-1">
                                    {{ $selectedMessage->created_at->format('M d, Y • h:i A') }}
                                </div>
                                
                                @if($selectedMessage->patient_id)
                                    <div class="mt-4 inline-flex items-center gap-2 bg-indigo-50 border border-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg text-xs font-semibold">
                                        <flux:icon.user class="w-4 h-4" />
                                        <span>Patient Chart: <a href="{{ route('patients.show', $selectedMessage->patient_id) }}" class="hover:underline">{{ $selectedMessage->patient->full_name }}</a></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button size="sm" variant="subtle" icon="arrow-uturn-left" class="rounded-lg">Reply</flux:button>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar flex-1">
                    <div class="prose max-w-none text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $selectedMessage->body }}</div>
                </div>
            @else
                <div class="h-full flex items-center justify-center flex-col p-8 text-center bg-slate-50/30">
                    <div class="w-16 h-16 bg-white rounded-full shadow-sm border border-slate-100 flex items-center justify-center mb-4">
                        <flux:icon.envelope-open class="w-8 h-8 text-slate-300" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1">Select a message</h3>
                    <p class="text-sm text-slate-500">Choose a message from the list to read it here.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Compose Modal -->
    <flux:modal wire:model="showCompose" class="md:w-[600px] !p-0 overflow-hidden">
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Compose Message</h3>
            <button wire:click="$set('showCompose', false)" class="text-slate-400 hover:text-slate-600 transition-colors">
                <flux:icon.x-mark class="w-5 h-5" />
            </button>
        </div>
        
        <div class="p-6 bg-white">
            <form wire:submit.prevent="sendMessage" class="space-y-5">
                <flux:field>
                    <flux:label class="font-semibold text-slate-700">To</flux:label>
                    <flux:select wire:model="recipientId" placeholder="Select recipient..." class="bg-slate-50 border-slate-200 focus:ring-indigo-500">
                        <option value="">Select a staff member...</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} ({{ ucfirst($member->role) }})</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="recipientId" />
                </flux:field>
                
                <flux:field>
                    <flux:label class="font-semibold text-slate-700">Regarding Patient (Optional)</flux:label>
                    <flux:select wire:model="patientId" class="bg-slate-50 border-slate-200 focus:ring-indigo-500">
                        <option value="">None</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }} (DOB: {{ $patient->date_of_birth->format('m/d/Y') }})</option>
                        @endforeach
                    </flux:select>
                </flux:field>
                
                <flux:field>
                    <flux:label class="font-semibold text-slate-700">Subject</flux:label>
                    <flux:input wire:model="subject" type="text" required class="bg-slate-50 border-slate-200 focus:ring-indigo-500" />
                </flux:field>
                
                <flux:field>
                    <flux:label class="font-semibold text-slate-700">Message</flux:label>
                    <flux:textarea wire:model="body" rows="6" required class="bg-slate-50 border-slate-200 focus:ring-indigo-500 custom-scrollbar" />
                </flux:field>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-slate-100">
                    <flux:button wire:click="$set('showCompose', false)" variant="subtle" class="rounded-xl">Cancel</flux:button>
                    <flux:button type="submit" variant="primary" class="rounded-xl shadow-sm px-6">Send Message</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
