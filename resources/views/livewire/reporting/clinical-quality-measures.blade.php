<flux:main class="space-y-6">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Clinical Quality Measures (CQM)</flux:heading>
            <flux:subheading>Track performance against clinical quality goals (MIPS/MACRA).</flux:subheading>
        </div>
        
        <div class="mt-4 sm:mt-0 flex items-center space-x-4">
            <flux:select wire:model.live="reportingPeriod" class="w-32">
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </flux:select>
            <flux:button wire:click="export" variant="outline" icon="arrow-down-tray">Export</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($measures as $measure)
            <flux:card>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-xs font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 px-2 py-1 rounded">{{ $measure['id'] }}</span>
                    @if($measure['rate'] >= $measure['target'])
                        <flux:badge color="green" icon="check-circle">Meeting Target</flux:badge>
                    @else
                        <flux:badge color="amber" icon="exclamation-triangle">Needs Improvement</flux:badge>
                    @endif
                </div>
                
                <h3 class="font-semibold text-zinc-900 dark:text-white mb-2">{{ $measure['name'] }}</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6 h-10 line-clamp-2" title="{{ $measure['description'] }}">
                    {{ $measure['description'] }}
                </p>
                
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Current Rate</span>
                        <span class="font-bold text-zinc-900 dark:text-white">{{ $measure['rate'] }}%</span>
                    </div>
                    
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $measure['rate'] >= $measure['target'] ? 'bg-green-500' : 'bg-amber-500' }}" style="width: {{ min(100, $measure['rate']) }}%"></div>
                    </div>
                    
                    <div class="flex justify-between text-xs text-zinc-500">
                        <span>{{ $measure['numerator'] }} / {{ $measure['denominator'] }} Patients</span>
                        <span>Target: {{ $measure['target'] }}%</span>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>
</flux:main>
