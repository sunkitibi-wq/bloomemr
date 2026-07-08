<?php

namespace App\Console\Commands;

use App\Models\Encounter;
use App\Notifications\UnsignedNoteReminderNotification;
use Illuminate\Console\Command;

class SendUnsignedNoteReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bloom:send-unsigned-note-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send in-app reminders to providers who have unsigned encounter notes older than 24 hours.';

    public function handle(): int
    {
        $encounters = Encounter::where('status', 'draft')
            ->where('created_at', '<', now()->subDay())
            ->with('provider')
            ->get();

        $count = 0;

        foreach ($encounters as $encounter) {
            if ($encounter->provider) {
                $encounter->provider->notify(new UnsignedNoteReminderNotification($encounter));
                $count++;
            }
        }

        $this->info("Sent {$count} unsigned note reminder(s).");

        return self::SUCCESS;
    }
}
