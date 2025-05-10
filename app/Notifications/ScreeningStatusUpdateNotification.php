<?php

namespace App\Notifications;

use App\Models\TenantScreening;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScreeningStatusUpdateNotification extends Notification
{
    use Queueable;

    protected $screening;
    protected $status;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TenantScreening $screening, $status)
    {
        $this->screening = $screening;
        $this->status = $status;
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
        $statusText = ucfirst($this->status);
        $unit = $this->screening->unit;
        $property = $unit->property;

        return (new MailMessage)
            ->subject("Tenant Screening Update: $statusText")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your tenant screening application for {$unit->unit_number} at {$property->name} has been updated.")
            ->line("Current status: $statusText")
            ->line($this->getStatusSpecificMessage())
            ->action('View Screening Details', url('/tenant/screenings'))
            ->line('Thank you for using NyumbaSmart!');
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
            'status' => $this->status,
            'message' => $this->getStatusSpecificMessage(),
        ];
    }

    /**
     * Get a message specific to the current status.
     *
     * @return string
     */
    protected function getStatusSpecificMessage()
    {
        switch($this->status) {
            case 'processing':
                return 'Your application is now being processed. We will notify you once the screening is complete.';
            case 'completed':
                return 'Your screening is complete. Please check your tenant dashboard for more details.';
            case 'approved':
                return 'Congratulations! Your application has been approved.';
            case 'rejected':
                return 'We regret to inform you that your application was not approved at this time.';
            default:
                return 'Your application status has been updated.';
        }
    }
}