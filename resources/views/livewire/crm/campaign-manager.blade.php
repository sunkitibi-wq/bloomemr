<flux:main class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">{{ __('Campaign Manager') }}</flux:heading>
            <flux:subheading>{{ __('Design outreach campaigns and assign tasks for patient cohorts.') }}</flux:subheading>
        </div>
    </div>

    @if (session()->has('message'))
        <flux:toast variant="success" text="{{ session('message') }}" />
    @endif
    @if (session()->has('error'))
        <flux:toast variant="danger" text="{{ session('error') }}" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('New Campaign') }}</flux:heading>
                <form wire:submit="generateCampaign" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Campaign Name') }}</flux:label>
                        <flux:input wire:model="campaignName" placeholder="e.g. Flu Shot Reminders" required />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Channel / Type') }}</flux:label>
                        <flux:select wire:model="campaignType" required>
                            <option value="email">Email</option>
                            <option value="sms">SMS Text</option>
                            <option value="call">Phone Call</option>
                            <option value="letter">Physical Letter</option>
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Target Condition / Keyword') }}</flux:label>
                        <flux:input wire:model="targetCondition" placeholder="e.g. Diabetes, Lisinopril (Leave blank for all)" />
                        <flux:description>{{ __('Filters patients based on notes or medications.') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Message Template') }}</flux:label>
                        <flux:textarea wire:model="message" rows="4" placeholder="Draft the message..." required />
                    </flux:field>

                    <flux:button type="submit" variant="primary" class="w-full">
                        {{ __('Launch Campaign') }}
                    </flux:button>
                </form>
            </flux:card>
        </div>

        <div class="lg:col-span-2">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Recent Outreach Tasks') }}</flux:heading>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-zinc-500">
                        <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-800/50 dark:text-zinc-300">
                            <tr>
                                <th class="px-4 py-3">{{ __('Title') }}</th>
                                <th class="px-4 py-3">{{ __('Patient') }}</th>
                                <th class="px-4 py-3">{{ __('Type') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3">{{ __('Due Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($engagements as $eng)
                                <tr class="border-b dark:border-zinc-700">
                                    <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                        {{ $eng->title }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('patients.show', $eng->patient_id) }}" class="text-primary hover:underline">
                                            {{ $eng->patient->full_name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 capitalize">{{ $eng->type }}</td>
                                    <td class="px-4 py-3">
                                        <flux:badge color="{{ $eng->status === 'completed' ? 'green' : 'zinc' }}">
                                            {{ ucfirst($eng->status) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-4 py-3">{{ $eng->due_date?->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-zinc-500">
                                        {{ __('No campaigns launched yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $engagements->links() }}
                </div>
            </flux:card>
        </div>
    </div>
</flux:main>
