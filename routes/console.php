<?php

use App\Console\Commands\SendUnsignedNoteReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send unsigned note reminders nightly at 8 PM
Schedule::command(SendUnsignedNoteReminders::class)->dailyAt('20:00');
