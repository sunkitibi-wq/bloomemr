<flux:main class="space-y-6">
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('billing.claims') }}" wire:navigate>{{ __('Claims Center') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Claim #') }}{{ $invoice->id }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Manage Claim') }}</flux:heading>
            <flux:subheading>
                {{ __('Invoice #') }}{{ $invoice->id }} &middot; 
                {{ $invoice->patient->full_name }} &middot; 
                ${{ number_format($invoice->total_amount, 2) }}
            </flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            @if($claim->status === 'draft')
                <flux:badge color="zinc">{{ __('Draft') }}</flux:badge>
                <flux:button variant="primary" wire:click="submitClaim" icon="paper-airplane">
                    {{ __('Submit to Clearinghouse') }}
                </flux:button>
            @elseif($claim->status === 'submitted')
                <flux:badge color="blue">{{ __('Submitted') }}</flux:badge>
            @elseif($claim->status === 'accepted')
                <flux:badge color="green">{{ __('Accepted') }}</flux:badge>
            @elseif($claim->status === 'rejected')
                <flux:badge color="red">{{ __('Rejected') }}</flux:badge>
            @endif
        </div>
    </div>

    @if (session()->has('message'))
        <flux:toast variant="success" text="{{ session('message') }}" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- EDI Request Panel -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('EDI 837P Request Preview') }}</flux:heading>
            <div class="bg-zinc-900 rounded-lg p-4 font-mono text-sm text-green-400 overflow-x-auto whitespace-pre">
{{ $claim->edi_request }}
            </div>
        </flux:card>

        <!-- EDI Response Panel -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Clearinghouse Response') }}</flux:heading>
            @if($claim->edi_response)
                <div class="bg-zinc-900 rounded-lg p-4 font-mono text-sm text-blue-400 overflow-x-auto whitespace-pre">
{{ $claim->edi_response }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-zinc-500">
                    <flux:icon.clock class="size-8 mb-2 opacity-50" />
                    <p>{{ __('No response received yet.') }}</p>
                </div>
            @endif
        </flux:card>
    </div>
</flux:main>
