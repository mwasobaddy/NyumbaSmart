<?php

use App\Jobs\ProcessBackgroundCheckStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\TenantScreening;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Define the scheduled tasks
Schedule::call(function () {
    TenantScreening::where('status', 'pending')
        ->whereNotNull('reference_id')
        ->get()
        ->each(function ($screening) {
            ProcessBackgroundCheckStatus::dispatch($screening);
        });
})->daily();
