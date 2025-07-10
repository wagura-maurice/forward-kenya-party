<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('process:outbound-bulk-text-messages')
    ->everyMinute()
    ->timezone('Africa/Nairobi');

Schedule::command('app:maintenance-password-reset')
    ->quarterly()
    ->timezone('Africa/Nairobi');

Schedule::command('app:optimize')
    ->dailyAt('00:00:00')
    ->timezone('Africa/Nairobi');