<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Secure Messaging') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('Communicate securely with your child\'s healthcare providers') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
            @if ($patient)
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-slate-800 text-base font-semibold">
                        {{ __('Inbox for :name', ['name' => $patient->full_name]) }}
                    </h3>
                    <flux:button variant="primary" icon="plus" wire:click="openNewForm">
                        {{ __('New Message') }}
                    </flux:button>
                </div>

                @if (session()->has('message'))
                    <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 bg-white border border-slate-200 rounded-2xl overflow-hidden min-h-[500px]">
                    <!-- Left: Message List -->
                    <div class="lg:col-span-1 border-r border-slate-200 flex flex-col">
                        <div class="p-4 border-b border-slate-200 bg-slate-50">
                            <flux:heading size="sm">{{ __('Conversations') }}</flux:heading>
                        </div>
                        <div class="flex-1 overflow-y-auto divide-y divide-slate-100 ">
                            @forelse ($messages as $msg)
                                @php
                                    $isSender = $msg->sender_id === auth('portal')->id();
                                    $otherParty = $isSender ? $msg->recipient : $msg->sender;
                                    $isUnread = !$isSender && is_null($msg->read_at);
                                    $isActive = $activeMessage && $activeMessage->id === $msg->id;
                                @endphp
                                <button wire:click="selectMessage({{ $msg->id }})" 
                                        class="w-full text-left p-4 hover:bg-slate-50 :bg-white flex flex-col gap-1 transition-colors {{ $isActive ? 'bg-slate-50' : '' }}">
                                    <div class="flex justify-between items-center w-full">
                                        <span class="text-xs font-semibold {{ $isUnread ? 'text-blue-600 font-bold' : 'text-slate-500' }}">
                                            {{ $otherParty?->name }}
                                        </span>
                                        <span class="text-[10px] text-slate-500">
                                            {{ $msg->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 justify-between w-full">
                                        <span class="text-sm font-semibold truncate text-slate-800 {{ $isUnread ? 'font-bold' : '' }}">
                                            {{ $msg->subject }}
                                        </span>
                                        @if ($isUnread)
                                            <span class="h-2 w-2 rounded-full bg-blue-600 shrink-0"></span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 truncate w-full">
                                        {{ $msg->body }}
                                    </p>
                                </button>
                            @empty
                                <p class="text-xs text-slate-500 py-8 italic text-center">{{ __('No messages found.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Right: Message Detail -->
                    <div class="lg:col-span-2 flex flex-col bg-slate-50/50">
                        @if ($activeMessage)
                            <div class="p-5 border-b border-slate-200 bg-white flex justify-between items-center">
                                <div>
                                    <flux:heading size="lg">{{ $activeMessage->subject }}</flux:heading>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ __('Between you and :provider', ['provider' => ($activeMessage->sender_id === auth('portal')->id() ? $activeMessage->recipient->name : $activeMessage->sender->name)]) }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Conversation Thread Box -->
                            <div class="flex-1 p-6 overflow-y-auto space-y-4">
                                <!-- Message body -->
                                <div class="flex flex-col gap-1 max-w-[85%] {{ $activeMessage->sender_id === auth('portal')->id() ? 'ml-auto items-end' : 'mr-auto items-start' }}">
                                    <span class="text-[10px] text-slate-500 font-semibold">
                                        {{ $activeMessage->sender->name }} • {{ $activeMessage->created_at->format('M j, g:i A') }}
                                    </span>
                                    <div class="p-4 rounded-2xl text-sm {{ $activeMessage->sender_id === auth('portal')->id() ? 'bg-primary text-white rounded-tr-none' : 'bg-white border border-slate-200 text-slate-800 rounded-tl-none shadow-xs' }}">
                                        {!! nl2br(e($activeMessage->body)) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Reply Box -->
                            <div class="p-4 border-t border-slate-200 bg-white ">
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
                            <div class="p-6 bg-white border border-slate-200 rounded-2xl m-6 shadow-xs">
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
                            <div class="flex-1 flex flex-col items-center justify-center text-slate-500 p-8">
                                <flux:icon.envelope class="size-12 text-slate-600 mb-3" />
                                <p class="text-sm font-semibold">{{ __('No conversation selected') }}</p>
                                <p class="text-xs mt-1 text-center max-w-xs">{{ __('Select a thread from the list on the left, or compose a new message to get in touch with your care provider.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-slate-500 italic">
                    {{ __('No patient records found.') }}
                </div>
            @endif
        </div>
</div>
