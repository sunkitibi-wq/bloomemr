<div class="mt-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden min-h-[500px] grid grid-cols-1 lg:grid-cols-3">
    <!-- Left: Conversations List -->
    <div class="lg:col-span-1 border-r border-zinc-200 dark:border-zinc-800 flex flex-col">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
            <flux:heading size="sm">{{ __('Secure Message Inbox') }}</flux:heading>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
            @forelse ($messages as $msg)
                @php
                    $isSender = $msg->sender_id === auth()->id();
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
                            <span class="h-2 w-2 rounded-full bg-blue-650 shrink-0"></span>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-400 truncate w-full">
                        {{ $msg->body }}
                    </p>
                </button>
            @empty
                <p class="text-xs text-zinc-450 p-8 italic text-center">{{ __('No secure messages found for this patient.') }}</p>
            @endforelse
        </div>
    </div>

    <!-- Right: Conversation Detail & Reply -->
    <div class="lg:col-span-2 flex flex-col bg-zinc-50/50 dark:bg-zinc-900/20">
        @if ($activeMessage)
            <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex justify-between items-center">
                <div>
                    <flux:heading size="lg">{{ $activeMessage->subject }}</flux:heading>
                    <p class="text-xs text-zinc-450 mt-1">
                        {{ __('Between you and patient guardian :guardian', ['guardian' => ($activeMessage->sender_id === auth()->id() ? $activeMessage->recipient->name : $activeMessage->sender->name)]) }}
                    </p>
                </div>
            </div>
            
            <div class="flex-1 p-6 overflow-y-auto space-y-4">
                <div class="flex flex-col gap-1 max-w-[85%] {{ $activeMessage->sender_id === auth()->id() ? 'ml-auto items-end' : 'mr-auto items-start' }}">
                    <span class="text-[10px] text-zinc-400 font-semibold">
                        {{ $activeMessage->sender->name }} • {{ $activeMessage->created_at->format('M j, g:i A') }}
                    </span>
                    <div class="p-4 rounded-2xl text-sm {{ $activeMessage->sender_id === auth()->id() ? 'bg-primary text-white rounded-tr-none' : 'bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-100 rounded-tl-none shadow-xs' }}">
                        {!! nl2br(e($activeMessage->body)) !!}
                    </div>
                </div>
            </div>

            <!-- Reply Box -->
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <form wire:submit.prevent="sendReply" class="space-y-3">
                    <flux:textarea wire:model="replyBody" placeholder="{{ __('Type reply to family here...') }}" rows="3" required />
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" icon="paper-airplane">
                            {{ __('Send Reply') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-zinc-400 p-8">
                <flux:icon.envelope class="size-12 text-zinc-305 mb-3" />
                <p class="text-sm font-semibold">{{ __('No message selected') }}</p>
                <p class="text-xs mt-1 text-center max-w-xs">{{ __('Select a message thread on the left to read history and reply to the patient.') }}</p>
            </div>
        @endif
    </div>
</div>
