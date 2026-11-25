<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Safety net for Zoom classes: fetch attendance for classes that finished (needs the server's cron job
// "php artisan schedule:run" every minute).
Schedule::command('zoom:sync')->everyFiveMinutes()->withoutOverlapping();

// Class recordings: ask Zoom for new ones and move them to YouTube, one at a time (a big video takes a while,
// so the lock lasts up to 4 hours).
Schedule::command('zoom:recordings')->everyMinute()->withoutOverlapping(240);
