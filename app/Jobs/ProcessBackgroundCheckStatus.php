<?php

namespace App\Jobs;

use App\Models\TenantScreening;
use App\Notifications\ScreeningStatusUpdateNotification;
use App\Services\BackgroundCheckService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBackgroundCheckStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The tenant screening instance.
     *
     * @var \App\Models\TenantScreening
     */
    protected $screening;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\TenantScreening  $screening
     * @return void
     */
    public function __construct(TenantScreening $screening)
    {
        $this->screening = $screening;
    }

    /**
     * Execute the job.
     *
     * @param  \App\Services\BackgroundCheckService  $backgroundCheckService
     * @return void
     */
    public function handle(BackgroundCheckService $backgroundCheckService)
    {
        // Skip if no reference ID or already completed
        if (empty($this->screening->reference_id) || $this->screening->isCompleted()) {
            return;
        }

        Log::info('Checking background check status for screening', [
            'screening_id' => $this->screening->id,
            'reference_id' => $this->screening->reference_id,
        ]);

        // Get the current status from the background check service
        $result = $backgroundCheckService->getCheckStatus($this->screening->reference_id);
        
        if (!$result['success']) {
            Log::error('Failed to get background check status', [
                'screening_id' => $this->screening->id,
                'error' => $result['message']
            ]);
            return;
        }

        // Update screening based on the check results
        $this->updateScreeningStatus($result);
    }

    /**
     * Update the screening status based on the background check result.
     *
     * @param  array  $result
     * @return void
     */
    protected function updateScreeningStatus(array $result)
    {
        $status = $result['status'] ?? 'pending';
        $reportData = $result['report_data'] ?? [];
        $previousStatus = $this->screening->status;
        $statusUpdated = false;
        
        // Only process if we have a definitive status
        if ($status === 'completed') {
            // Update individual check results
            $this->screening->credit_check_passed = 
                ($reportData['credit_check']['status'] ?? 'failed') === 'passed';
            
            $this->screening->background_check_passed = 
                ($reportData['criminal_check']['status'] ?? 'failed') === 'passed';
            
            $this->screening->eviction_check_passed = 
                ($reportData['eviction_check']['status'] ?? 'failed') === 'passed';
            
            $this->screening->employment_verified = 
                ($reportData['employment_verification']['status'] ?? 'failed') === 'verified';
            
            // If income verification exists, update it
            if (isset($reportData['income_verification'])) {
                $this->screening->income_verified = 
                    $reportData['income_verification']['status'] === 'verified';
            }
            
            // Store the full report data
            $this->screening->report_data = json_encode($reportData);
            
            // Update status based on checks
            $allPassed = $this->screening->credit_check_passed && 
                         $this->screening->background_check_passed && 
                         $this->screening->eviction_check_passed;
            
            $this->screening->status = $allPassed ? 'approved' : 'rejected';
            $this->screening->completed_at = now();
            
            $statusUpdated = true;
        } elseif ($status === 'in_progress' && $previousStatus === 'pending') {
            $this->screening->status = 'in_progress';
            $statusUpdated = true;
        }
        
        // Save changes
        if ($statusUpdated) {
            $this->screening->save();
            
            // Notify the tenant about the status update
            if ($this->screening->tenant) {
                $this->screening->tenant->notify(
                    new ScreeningStatusUpdateNotification($this->screening, $this->screening->status)
                );
                
                // Also notify landlord if approved or rejected
                if (in_array($this->screening->status, ['approved', 'rejected']) && $this->screening->landlord) {
                    $this->screening->landlord->notify(
                        new ScreeningStatusUpdateNotification($this->screening, $this->screening->status)
                    );
                }
            }
        }
    }
}