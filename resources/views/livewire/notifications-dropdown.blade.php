<flux:dropdown position="bottom" align="end">
    <button class="relative p-1 rounded-full text-slate-400 hover:text-slate-600 transition-colors focus:outline-none focus:bg-slate-100">
        <span class="sr-only">View notifications</span>
        <flux:icon.bell class="h-6 w-6" variant="outline" />
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 block h-3.5 w-3.5 rounded-full bg-red-500 ring-2 ring-white text-[9px] font-bold text-white flex items-center justify-center leading-none">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
    <flux:menu class="w-80 p-0">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-[13px] font-bold text-slate-800">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Mark all as read</button>
            @endif
        </div>
        <div class="max-h-80 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-3 border-b border-slate-50 last:border-b-0 hover:bg-slate-50 transition-colors relative group {{ is_null($notification->read_at) ? 'bg-indigo-50/30' : '' }}">
                    @php
                        // Format the notification data for display
                        $type = class_basename($notification->type);
                        $message = $notification->data['message'] ?? 'New notification';
                        $icon = 'bell';
                        $color = 'text-indigo-500';
                        $bg = 'bg-indigo-100';
                        
                        if (str_contains($type, 'Appointment')) {
                            $icon = 'calendar';
                            $color = 'text-emerald-500';
                            $bg = 'bg-emerald-100';
                        } elseif (str_contains($type, 'Lab')) {
                            $icon = 'beaker';
                            $color = 'text-sky-500';
                            $bg = 'bg-sky-100';
                        } elseif (str_contains($type, 'Message')) {
                            $icon = 'chat-bubble-left-ellipsis';
                            $color = 'text-purple-500';
                            $bg = 'bg-purple-100';
                        } elseif (str_contains($type, 'Refill')) {
                            $icon = 'clipboard-document-check';
                            $color = 'text-amber-500';
                            $bg = 'bg-amber-100';
                        }
                    @endphp
                    
                    <div class="flex gap-3">
                        <div class="flex-none w-8 h-8 rounded-full {{ $bg }} flex items-center justify-center shrink-0">
                            <flux:icon name="{{ $icon }}" class="w-4 h-4 {{ $color }}" variant="outline" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] text-slate-700 font-medium leading-snug">
                                {{ $message }}
                            </p>
                            <p class="text-[11px] text-slate-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="shrink-0 flex items-start">
                            <button wire:click.stop="markAsRead('{{ $notification->id }}')" class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded transition-all" title="Mark as read">
                                <flux:icon.check class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center flex flex-col items-center justify-center">
                    <flux:icon.bell-snooze class="w-8 h-8 text-slate-300 mb-2" variant="outline" />
                    <p class="text-[13px] font-medium text-slate-500">You're all caught up!</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">No new notifications</p>
                </div>
            @endforelse
        </div>
    </flux:menu>
</flux:dropdown>
