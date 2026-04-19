<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scrape new jobs every hour
Schedule::command('jobs:scrape')->hourly();

// Send job alert emails daily at 8:00 AM
Schedule::command('jobs:send-alerts')->dailyAt('08:00');
