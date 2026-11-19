<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan foto yatim setiap hari (butuh scheduler berjalan: `php artisan schedule:work` atau cron schedule:run)
Schedule::command('photos:cleanup --apply')->dailyAt('02:30');
