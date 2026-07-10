<flux:main class="space-y-6 h-full flex flex-col">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Message Center</flux:heading>
            <flux:subheading>Secure intra-clinic messaging and patient communications.</flux:subheading>
        </div>
        
        <div class="mt-4 sm:mt-0 flex items-center space-x-4">
            <flux:button wire:click="openCompose" variant="primary" icon="pencil-square">
                Compose
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-[calc(100vh-140px)] min-h-[500px]">
        
        <!-- Sidebar: Message List -->
        <flux:card class="md:col-span-1 flex flex-col overflow-hidden h-full p-0">
            <div class="border-b border-zinc-200 dark:border-zinc-700 flex">
                <button wire:click="$set('activeTab', 'inbox')" class="flex-1 py-3 text-sm font-medium text-center border-b-2 transition-colors {{ $activeTab === 'inbox' ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Inbox
                </button>
                <button wire:click="$set('activeTab', 'sent')" class="flex-1 py-3 text-sm font-medium text-center border-b-2 transition-colors {{ $activeTab === 'sent' ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Sent
                </button>
            </div>
            
            <div class="overflow-y-auto flex-1">
                @forelse($messages as $msg)
                    <div wire:click="selectMessage({{ $msg->id }})" class="p-4 border-b border-zinc-200 dark:border-zinc-800 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ $selectedMessageId === $msg->id ? 'bg-zinc-100 dark:bg-zinc-800' : '' }} {{ null === $msg->read_at && $activeTab === 'inbox' ? 'font-bold' : '' }}">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm truncate pr-2 dark:text-zinc-200">
                                {{ $activeTab === 'inbox' ? $msg->sender->name : $msg->recipient->name }}
                            </span>
                            <span class="text-xs text-zinc-500 whitespace-nowrap">{{ $msg->created_at->format('M d') }}</span>
                        </div>
                        <div class="text-sm text-zinc-800 dark:text-zinc-300 truncate">
                            {{ $msg->subject }}
                        </div>
                        @if($msg->patient_id)
                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1 flex items-center">
                                <flux:icon.user class="size-3 mr-1" /> {{ $msg->patient->full_name }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-sm text-zinc-500">
                        No messages in {{ $activeTab }}.
                    </div>
                @endforelse
            </div>
            @if($messages->hasPages())
                <div class="p-3 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $messages->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </flux:card>

        <!-- Main Content: Message Detail -->
        <flux:card class="md:col-span-2 h-full overflow-y-auto">
            @if($selectedMessage)
                <div class="mb-6 border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ $selectedMessage->subject }}</h2>
                    
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm">
                                <span class="text-zinc-500">From:</span> 
                                <span class="font-medium text-zinc-900 dark:text-zinc-200">{{ $selectedMessage->sender->name }}</span>
                            </div>
                            <div class="text-sm mt-1">
                                <span class="text-zinc-500">To:</span> 
                                <span class="font-medium text-zinc-900 dark:text-zinc-200">{{ $selectedMessage->recipient->name }}</span>
                            </div>
                            @if($selectedMessage->patient_id)
                                <div class="text-sm mt-2 flex items-center bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-md w-fit">
                                    <flux:icon.user class="size-4 mr-2" />
                                    <span>Patient Chart: <a href="{{ route('patients.show', $selectedMessage->patient_id) }}" class="font-medium hover:underline">{{ $selectedMessage->patient->full_name }}</a></span>
                                </div>
                            @endif
                        </div>
                        <div class="text-sm text-zinc-500">
                            {{ $selectedMessage->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
                
                <div class="prose dark:prose-invert max-w-none text-sm whitespace-pre-wrap">
                    {{ $selectedMessage->body }}
                </div>
            @else
                <div class="h-full flex items-center justify-center text-zinc-500 flex-col">
                    <flux:icon.envelope class="size-12 mb-4 text-zinc-300 dark:text-zinc-700" />
                    <p>Select a message to read</p>
                </div>
            @endif
        </flux:card>
    </div>

    <!-- Compose Modal -->
    <flux:modal wire:model="showCompose" class="md:w-[600px]">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">Compose Message</flux:heading>
            
            <form wire:submit.prevent="sendMessage" class="space-y-4">
                <flux:field>
                    <flux:label>To</flux:label>
                    <flux:select wire:model="recipientId" placeholder="Select recipient...">
                        <option value="">Select a staff member...</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} ({{ ucfirst($member->role) }})</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="recipientId" />
                </flux:field>
                
                <flux:field>
                    <flux:label>Regarding Patient (Optional)</flux:label>
                    <flux:select wire:model="patientId">
                        <option value="">None</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }} (DOB: {{ $patient->date_of_birth->format('m/d/Y') }})</option>
                        @endforeach
                    </flux:select>
                </flux:field>
                
                <flux:field>
                    <flux:label>Subject</flux:label>
                    <flux:input wire:model="subject" type="text" required />
                </flux:field>
                
                <flux:field>
                    <flux:label>Message</flux:label>
                    <flux:textarea wire:model="body" rows="6" required />
                </flux:field>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="$set('showCompose', false)" variant="subtle">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Send Message</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</flux:main>
