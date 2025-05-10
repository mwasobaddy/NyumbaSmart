<?php

use App\Jobs\ProcessBackgroundCheckStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Define the scheduled tasks
Schedule::job(new ProcessBackgroundCheckStatus)->daily();
