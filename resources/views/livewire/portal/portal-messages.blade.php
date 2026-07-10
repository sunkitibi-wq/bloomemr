<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col md:flex-row">
    <!-- Sidebar Navigation -->
    <x-portal.sidebar />

    <!-- Main Workspace Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top bar -->
        <header class="h-20 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800 flex justify-between items-center px-8">
            <div>
                <h1 class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 text-lg">
                    {{ __('Secure Messaging') }}
                </h1>
                <p class="text-xs text-zinc-400">
                    {{ __('Communicate securely with your child\'s healthcare providers') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-semibold text-trust-navy dark:text-zinc-100">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-zinc-400 uppercase tracking-wider">{{ __('Primary Guardian') }}</p>
                </div>
                <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-700">
                    {{ auth()->user()->initials() }}
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto flex flex-col">
            @if ($patient)
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-headline-md text-trust-navy dark:text-zinc-100 text-base font-semibold">
                        {{ __('Inbox for :name', ['name' => $patient->full_name]) }}
                    </h3>
                    <flux:button variant="primary" icon="plus" wire:click="openNewForm">
                        {{ __('New Message') }}
                    </flux:button>
                </div>

                @if (session()->has('message'))
                    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden min-h-[500px]">
                    <!-- Left: Message List -->
                    <div class="lg:col-span-1 border-r border-zinc-200 dark:border-zinc-800 flex flex-col">
                        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                            <flux:heading size="sm">{{ __('Conversations') }}</flux:heading>
                        </div>
                        <div class="flex-1 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse ($messages as $msg)
                                @php
                                    $isSender = $msg->sender_id === auth('portal')->id();
                                    $otherParty = $isSender ? $msg->recipient : $msg->sender;
                                    $isUnread = !$isSender && is_null($msg->read_at);
                                    $isActive = $activeMessage && $activeMessage->id === $msg->id;
                                @endphp
                                <button wire:click="selectMessage({{ $msg->id }})" 
                                        class="w-full text-left p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/20 flex flex-col gap-1 transition-colors {{ $isActive ? 'bg-slate-50 dark:bg-zinc-800/40' : '' }}">
                                    <div class="flex justify-between items-center w-full">
                                        <span class="text-xs font-semibold {{ $isUnread ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-zinc-500' }}">
                                            {{ $otherParty?->name }}
                                        </span>
                                        <span class="text-[10px] text-zinc-400">
                                            {{ $msg->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 justify-between w-full">
                                        <span class="text-sm font-semibold truncate text-zinc-800 dark:text-zinc-100 {{ $isUnread ? 'font-bold' : '' }}">
                                            {{ $msg->subject }}
                                        </span>
                                        @if ($isUnread)
                                            <span class="h-2 w-2 rounded-full bg-blue-600 shrink-0"></span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-zinc-400 truncate w-full">
                                        {{ $msg->body }}
                                    </p>
                                </button>
                            @empty
                                <p class="text-xs text-zinc-400 py-8 italic text-center">{{ __('No messages found.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Right: Message Detail -->
                    <div class="lg:col-span-2 flex flex-col bg-zinc-50/50 dark:bg-zinc-900/20">
                        @if ($activeMessage)
                            <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex justify-between items-center">
                                <div>
                                    <flux:heading size="lg">{{ $activeMessage->subject }}</flux:heading>
                                    <p class="text-xs text-zinc-450 mt-1">
                                        {{ __('Between you and :provider', ['provider' => ($activeMessage->sender_id === auth('portal')->id() ? $activeMessage->recipient->name : $activeMessage->sender->name)]) }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Conversation Thread Box -->
                            <div class="flex-1 p-6 overflow-y-auto space-y-4">
                                <!-- Message body -->
                                <div class="flex flex-col gap-1 max-w-[85%] {{ $activeMessage->sender_id === auth('portal')->id() ? 'ml-auto items-end' : 'mr-auto items-start' }}">
                                    <span class="text-[10px] text-zinc-400 font-semibold">
                                        {{ $activeMessage->sender->name }} • {{ $activeMessage->created_at->format('M j, g:i A') }}
                                    </span>
                                    <div class="p-4 rounded-2xl text-sm {{ $activeMessage->sender_id === auth('portal')->id() ? 'bg-primary text-white rounded-tr-none' : 'bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-100 rounded-tl-none shadow-xs' }}">
                                        {!! nl2br(e($activeMessage->body)) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Reply Box -->
                            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                                <form wire:submit.prevent="sendReply" class="space-y-3">
                                    <div class="flex gap-2 mb-2">
                                        <div class="flex-1">
                                            <flux:input wire:model="aiPrompt" placeholder="{{ __('Ask AI to draft a reply (e.g. Please refill my lexapro)...') }}" />
                                        </div>
                                        <flux:button wire:click="draftAiMessage(true)" variant="subtle" icon="sparkles" wire:loading.attr="disabled">
                                            {{ __('Draft with AI') }}
                                        </flux:button>
                                    </div>
                                    <flux:textarea wire:model="replyBody" placeholder="{{ __('Type your reply here...') }}" rows="3" required />
                                    <div class="flex justify-end">
                                        <flux:button type="submit" variant="primary" icon="paper-airplane">
                                            {{ __('Send Reply') }}
                                        </flux:button>
                                    </div>
                                </form>
                            </div>
                        @elseif ($isNewFormOpen)
                            <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl m-6 shadow-xs">
                                <form wire:submit.prevent="sendMessage" class="space-y-4">
                                    <flux:heading size="lg">{{ __('Compose Secure Message') }}</flux:heading>
                                    <flux:subheading>{{ __('Send an encrypted message directly to your clinical provider.') }}</flux:subheading>
                                    
                                    <flux:select wire:model="providerId" :label="__('Choose Recipient Provider')" required>
                                        <option value="">{{ __('Select Clinician...') }}</option>
                                        @foreach ($providers as $prov)
                                            <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="providerId" />

                                    <flux:input wire:model="subject" :label="__('Subject')" placeholder="{{ __('e.g., Medication side effects, Scheduling request...') }}" required />
                                    <flux:error name="subject" />

                                    <div class="flex gap-2 mb-2 items-end">
                                        <div class="flex-1">
                                            <flux:input wire:model="aiPrompt" :label="__('AI Message Drafter')" placeholder="{{ __('Ask AI to draft this message (e.g. Need to reschedule my visit)...') }}" />
                                        </div>
                                        <flux:button wire:click="draftAiMessage(false)" variant="subtle" icon="sparkles" wire:loading.attr="disabled">
                                            {{ __('Draft with AI') }}
                                        </flux:button>
                                    </div>
                                    <flux:textarea wire:model="body" :label="__('Message Body')" rows="6" placeholder="{{ __('Provide detailed information to help your provider address your concerns...') }}" required />
                                    <flux:error name="body" />

                                    <div class="flex justify-end space-x-2 pt-2">
                                        <flux:button variant="ghost" wire:click="$set('isNewFormOpen', false)">{{ __('Cancel') }}</flux:button>
                                        <flux:button variant="primary" type="submit">{{ __('Send Message') }}</flux:button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center text-zinc-400 p-8">
                                <flux:icon.envelope class="size-12 text-zinc-300 mb-3" />
                                <p class="text-sm font-semibold">{{ __('No conversation selected') }}</p>
                                <p class="text-xs mt-1 text-center max-w-xs">{{ __('Select a thread from the list on the left, or compose a new message to get in touch with your care provider.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-zinc-400 italic">
                    {{ __('No patient records found.') }}
                </div>
            @endif
        </main>
    </div>
</div>
