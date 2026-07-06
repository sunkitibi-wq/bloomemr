<div class="mt-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
    <flux:heading size="lg">{{ __('Portal Refill Requests') }}</flux:heading>

    <div class="divide-y divide-slate-100 dark:divide-zinc-800">
        @forelse ($refillRequests as $req)
            <div class="py-4 first:pt-0 last:pb-0 flex items-start justify-between">
                <div class="space-y-1">
                    <p class="text-sm font-bold text-trust-navy dark:text-zinc-100">
                        {{ $req->medication->name }} ({{ $req->medication->dose }})
                    </p>
                    <p class="text-xs text-zinc-400">
                        {{ __('Requested by: :user', ['user' => $req->requestedBy->name]) }} &middot; {{ $req->created_at->format('M j, Y, g:i A') }}
                    </p>
                    @if ($req->notes)
                        <p class="text-xs text-zinc-500 bg-zinc-50 dark:bg-zinc-800/40 p-2.5 rounded-lg italic mt-1.5">
                            "{{ $req->notes }}"
                        </p>
                    @endif
                </div>
                
                <div class="flex items-center gap-3">
                    @if ($req->status === 'pending')
                        <flux:button size="sm" variant="primary" class="bg-green-600 hover:bg-green-700 text-white font-bold" wire:click="approve({{ $req->id }})">
                            {{ __('Approve & Prescribe') }}
                        </flux:button>
                        <flux:button size="sm" variant="danger" wire:click="deny({{ $req->id }})">
                            {{ __('Deny') }}
                        </flux:button>
                    @else
                        @php
                            $statusColors = [
                                'approved' => 'bg-green-105 text-green-800 dark:bg-green-905/30 dark:text-green-300',
                                'denied' => 'bg-red-105 text-red-800 dark:bg-red-905/30 dark:text-red-300',
                            ];
                            $color = $statusColors[$req->status] ?? 'bg-zinc-100 text-zinc-800';
                        @endphp
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $color }}">
                            {{ __(ucfirst($req->status)) }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-zinc-450 py-8 italic text-center">{{ __('No refill requests found.') }}</p>
        @endforelse
    </div>
</div>
