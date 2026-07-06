<flux:main class="space-y-8 bg-[#f7f9fb] dark:bg-zinc-950 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <flux:badge size="sm" color="red">{{ __('System Admin') }}</flux:badge>
            </div>
            <flux:heading size="xl" class="text-trust-navy dark:text-zinc-100 font-bold tracking-tight">
                {{ __('Practice Manager') }}
            </flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('View and manage all practices on this platform.') }}
            </flux:text>
        </div>

        @if (session()->has('current_practice_id'))
            <flux:button wire:click="stopImpersonating" variant="danger">
                <flux:icon.arrow-left class="size-4 mr-1.5" />
                {{ __('Stop Impersonating') }}
            </flux:button>
        @endif
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50/70 dark:bg-green-950/20 dark:border-green-800 p-4">
            <flux:icon.check-circle class="size-5 text-green-600 shrink-0" />
            <span class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-primary/5 dark:bg-primary/10 text-primary rounded-xl">
                <flux:icon.building-office class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Total Practices') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->practices->count() }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-green-50 dark:bg-green-950/30 text-green-600 rounded-xl">
                <flux:icon.check-circle class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Active') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->practices->where('is_active', true)->count() }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-red-50 dark:bg-red-950/30 text-red-500 rounded-xl">
                <flux:icon.x-circle class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Suspended') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->practices->where('is_active', false)->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Practice Table -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xs overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5">{{ __('Practice') }}</th>
                    <th class="px-6 py-3.5">{{ __('Slug') }}</th>
                    <th class="px-6 py-3.5 text-center">{{ __('Users') }}</th>
                    <th class="px-6 py-3.5 text-center">{{ __('Patients') }}</th>
                    <th class="px-6 py-3.5">{{ __('Status') }}</th>
                    <th class="px-6 py-3.5">{{ __('Created') }}</th>
                    <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($this->practices as $practice)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors" wire:key="practice-{{ $practice->id }}">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $practice->name }}</div>
                            <div class="text-xs text-zinc-400 mt-0.5">{{ __('ID: :id', ['id' => $practice->id]) }}</div>
                        </td>
                        <td class="px-6 py-4 text-zinc-500 font-mono text-xs">{{ $practice->slug }}</td>
                        <td class="px-6 py-4 text-center font-mono font-bold text-zinc-900 dark:text-zinc-100">{{ $practice->users_count }}</td>
                        <td class="px-6 py-4 text-center font-mono font-bold text-zinc-900 dark:text-zinc-100">{{ $practice->patients_count }}</td>
                        <td class="px-6 py-4">
                            <flux:badge size="sm" color="{{ $practice->is_active ? 'green' : 'red' }}">
                                {{ $practice->is_active ? __('Active') : __('Suspended') }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 text-xs text-zinc-400">{{ $practice->created_at->format('M j, Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <flux:button size="sm" variant="ghost" wire:click="impersonate({{ $practice->id }})">
                                <flux:icon.arrow-right-end-on-rectangle class="size-3.5 mr-1" />
                                {{ __('Enter') }}
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="ghost"
                                wire:click="togglePracticeStatus({{ $practice->id }})"
                                class="{{ $practice->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }}"
                            >
                                {{ $practice->is_active ? __('Suspend') : __('Activate') }}
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-zinc-400 italic text-sm">
                            {{ __('No practices found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</flux:main>
