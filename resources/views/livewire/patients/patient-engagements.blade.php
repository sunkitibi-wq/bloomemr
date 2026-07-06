<div class="space-y-6">
    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="lg" class="mb-2">{{ __('Patient Outreach & Follow-Up') }}</flux:heading>
        <flux:subheading>{{ __('Create and track patient engagement tasks such as calls, portal messages, or reminder follow-ups.') }}</flux:subheading>

        <div class="mt-4 space-y-4">
            <flux:input wire:model="title" :label="__('Title')" placeholder="e.g. Call patient about no-show follow-up" />
            <flux:textarea wire:model="description" :label="__('Description')" rows="3" placeholder="Add context for the outreach task" />
            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model="type" :label="__('Engagement Type')">
                    <option value="call">{{ __('Phone Call') }}</option>
                    <option value="text">{{ __('Text Message') }}</option>
                    <option value="email">{{ __('Email') }}</option>
                    <option value="portal_message">{{ __('Portal Message') }}</option>
                </flux:select>
                <flux:input type="date" wire:model="dueDate" :label="__('Due Date')" />
            </div>
            <div class="flex justify-end">
                <flux:button variant="primary" wire:click="createEngagement">
                    {{ __('Create Task') }}
                </flux:button>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="lg" class="mb-4">{{ __('Open Engagement Tasks') }}</flux:heading>

        <div class="space-y-3">
            @forelse ($patient->engagements as $engagement)
                <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $engagement->title }}</div>
                        <div class="text-sm text-zinc-500">{{ $engagement->description }}</div>
                        <div class="mt-2 text-xs text-zinc-400">
                            {{ ucfirst(str_replace('_', ' ', $engagement->type)) }} &middot; {{ __('Due') }} {{ $engagement->due_date?->format('M j, Y') }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:badge size="sm" color="{{ $engagement->status === 'completed' ? 'green' : 'amber' }}">
                            {{ ucfirst($engagement->status) }}
                        </flux:badge>
                        @if ($engagement->status !== 'completed')
                            <flux:button size="sm" variant="ghost" wire:click="completeEngagement({{ $engagement->id }})">
                                {{ __('Complete') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-zinc-400 italic">{{ __('No engagement tasks yet.') }}</p>
            @endforelse
        </div>
    </div>
</div>
