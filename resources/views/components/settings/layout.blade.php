<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('smart-phrases.edit')" wire:navigate>{{ __('Smart Phrases') }}</flux:navlist.item>
            <flux:navlist.item :href="route('security.edit')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
            @can('manage_users')
                <flux:navlist.item :href="route('settings.users')" wire:navigate>{{ __('User Management') }}</flux:navlist.item>
            @endcan
            @if (auth()->user()?->isSuperAdmin())
                <flux:navlist.item :href="route('settings.roles')" wire:navigate>{{ __('Roles & Permissions') }}</flux:navlist.item>
                <flux:navlist.item :href="route('settings.practice')" wire:navigate>{{ __('Practice Settings') }}</flux:navlist.item>
            @endif
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full {{ $attributes->get('class') ?: 'max-w-lg' }}">
            {{ $slot }}
        </div>
    </div>
</div>
