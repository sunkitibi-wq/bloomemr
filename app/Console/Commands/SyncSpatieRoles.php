<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SyncSpatieRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bloom:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Spatie roles and permissions for all existing users based on their role column';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting Spatie roles synchronization for all users...');

        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $roleName = $user->role;

            if (empty($roleName)) {
                $this->warn("User {$user->name} (ID: {$user->id}) has no role defined. Skipping.");

                continue;
            }

            // Ensure Spatie role exists
            Role::findOrCreate($roleName);

            // Sync the user role
            $user->syncRoles($roleName);
            $count++;

            $this->line("Synced role '{$roleName}' for user {$user->name} (Email: {$user->email})");
        }

        $this->info("Completed! Successfully synchronized Spatie roles for {$count} users.");
    }
}
