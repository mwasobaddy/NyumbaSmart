<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBackgroundCheckStatus;
use App\Models\TenantScreening;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckBackgroundStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:background-status {--id= : Process a specific screening ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check background status for pending tenant screenings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $specificId = $this->option('id');
        
        if ($specificId) {
            // Process a specific screening
            $screening = TenantScreening::find($specificId);
            
            if (!$screening) {
                $this->error("Screening with ID {$specificId} not found");
                return 1;
            }
            
            $this->processScreening($screening);
        } else {
            // Process all pending/in progress screenings with reference IDs
            $query = TenantScreening::whereNotNull('reference_id')
                ->whereIn('status', ['pending', 'in_progress'])
                ->where(function ($query) {
                    // Exclude screenings that were updated in the last hour
                    $query->whereNull('updated_at')
                        ->orWhere('updated_at', '<', now()->subHour());
                });
            
            $count = $query->count();
            $this->info("Found {$count} pending screenings to check");
            
            if ($count === 0) {
                return 0;
            }
            
            $query->chunk(10, function ($screenings) {
                foreach ($screenings as $screening) {
                    $this->processScreening($screening);
                }
            });
        }
        
        return 0;
    }
    
    /**
     * Process an individual screening.
     *
     * @param  \App\Models\TenantScreening  $screening
     * @return void
     */
    protected function processScreening(TenantScreening $screening)
    {
        $this->info("Processing screening #{$screening->id}");
        
        try {
            // Dispatch the job to process this screening
            ProcessBackgroundCheckStatus::dispatch($screening);
        } catch (\Exception $e) {
            $this->error("Error processing screening #{$screening->id}: " . $e->getMessage());
            Log::error("Error in CheckBackgroundStatus command", [
                'screening_id' => $screening->id,
                'error' => $e->getMessage(),
                'exception' => $e
            ]);
        }
    }
}