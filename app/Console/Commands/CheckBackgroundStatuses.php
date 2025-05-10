<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBackgroundCheckStatus;
use App\Models\TenantScreening;
use Illuminate\Console\Command;

class CheckBackgroundStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'screenings:check-background-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process background check statuses for all pending tenant screenings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $screenings = TenantScreening::where('status', 'in_progress')
            ->orWhere('status', 'pending')
            ->whereNotNull('background_check_reference_id')
            ->get();

        $count = $screenings->count();
        
        if ($count === 0) {
            $this->info('No pending tenant screenings to process');
            return 0;
        }
        
        $this->info("Processing {$count} tenant screening(s)...");
        
        foreach ($screenings as $screening) {
            ProcessBackgroundCheckStatus::dispatch($screening);
            $this->line("Queued job for screening #{$screening->id}");
        }
        
        $this->info('All background check status jobs have been queued');
        return 0;
    }
}