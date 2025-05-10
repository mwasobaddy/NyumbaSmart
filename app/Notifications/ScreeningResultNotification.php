<?php

namespace App\Notifications;

use App\Models\TenantScreening;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScreeningResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $screening;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TenantScreening $screening)
    {
        $this->screening = $screening;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $unit = $this->screening->unit;
        $property = $unit->property;
        $reportData = json_decode($this->screening->report_data ?? '{}', true);
        
        $mailMessage = (new MailMessage)
            ->subject('Your Tenant Screening Results Are Ready')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your background check for {$unit->unit_number} at {$property->name} has been completed.")
            ->action('View Full Results', url('/tenant/screenings/' . $this->screening->id));

        // Include summary information if available
        if (isset($reportData['summary'])) {
            $mailMessage->line('Summary of findings:')
                ->line($reportData['summary']);
        }

        return $mailMessage->line('Thank you for using NyumbaSmart!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'screening_id' => $this->screening->id,
            'unit_id' => $this->screening->unit_id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'message' => 'Your tenant screening report is now ready to view.',
        ];
    }
}