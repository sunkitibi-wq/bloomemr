<flux:main>
    <section class="w-full">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Practice Settings')" :subheading="__('Manage your practice name, timezone, and regional preferences.')">
            <form wire:submit.prevent="save" class="my-6 w-full space-y-6">
                <flux:field>
                    <flux:label>{{ __('Practice Name') }}</flux:label>
                    <flux:input wire:model="practiceName" placeholder="Bloom Child Psychiatry" required />
                    @error('practiceName') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Timezone') }}</flux:label>
                    <flux:select wire:model="timezone">
                        <option value="America/New_York">{{ __('Eastern Time (UTC-5)') }}</option>
                        <option value="America/Chicago">{{ __('Central Time (UTC-6)') }}</option>
                        <option value="America/Denver">{{ __('Mountain Time (UTC-7)') }}</option>
                        <option value="America/Los_Angeles">{{ __('Pacific Time (UTC-8)') }}</option>
                        <option value="America/Anchorage">{{ __('Alaska Time (UTC-9)') }}</option>
                        <option value="Pacific/Honolulu">{{ __('Hawaii Time (UTC-10)') }}</option>
                        <option value="Europe/London">{{ __('London (UTC+0)') }}</option>
                        <option value="Europe/Paris">{{ __('Paris (UTC+1)') }}</option>
                        <option value="Asia/Tokyo">{{ __('Tokyo (UTC+9)') }}</option>
                    </flux:select>
                    @error('timezone') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Locale / Language') }}</flux:label>
                    <flux:select wire:model="locale">
                        <option value="en">{{ __('English') }}</option>
                        <option value="es">{{ __('Spanish') }}</option>
                        <option value="fr">{{ __('French') }}</option>
                        <option value="de">{{ __('German') }}</option>
                    </flux:select>
                    @error('locale') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <div class="flex items-center gap-4">
                    <flux:button type="submit" variant="primary">{{ __('Save Practice Settings') }}</flux:button>
                </div>
            </form>
        </x-settings.layout>
    </section>
</flux:main>
