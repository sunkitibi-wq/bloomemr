<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">System Dashboard</h1>
        <p class="text-sm text-slate-500">Overview of your SaaS platform</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                    <flux:icon.building-office-2 class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-sm text-slate-500 font-medium">Total Tenants</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['total_practices'] }}</div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-sm text-slate-500 font-medium">Active Tenants</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['active_practices'] }}</div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-100 text-indigo-600 rounded-lg">
                    <flux:icon.users class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-sm text-slate-500 font-medium">Total Users</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['total_users'] }}</div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-100 text-orange-600 rounded-lg">
                    <flux:icon.shield-exclamation class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-sm text-slate-500 font-medium">Pending KYC</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['pending_kyc'] }}</div>
                </div>
            </div>
        </flux:card>
    </div>

    <flux:card>
        <flux:heading size="lg">Quick Actions</flux:heading>
        <div class="mt-4 flex gap-4">
            <flux:button href="{{ route('system.practices') }}" wire:navigate variant="primary">Manage Tenants</flux:button>
            <flux:button href="{{ route('system.kyc') }}" wire:navigate variant="outline">Review KYC</flux:button>
        </div>
    </flux:card>
</div>
