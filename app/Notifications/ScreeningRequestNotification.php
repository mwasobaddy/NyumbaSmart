<?php

namespace App\Notifications;

use App\Models\TenantScreening;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScreeningRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $screening;

    /**
     * Create a new notification instance.
     */
    public function __construct(TenantScreening $screening)
    {
        $this->screening = $screening;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $unit = $this->screening->unit;
        $property = $unit->property;
        
        return (new MailMessage)
            ->subject('New Tenant Screening Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a screening request for:')
            ->line('Property: ' . $property->name)
            ->line('Unit: ' . $unit->unit_number)
            ->line('Please complete your profile information and upload the required documents to proceed with the screening process.')
            ->action('View Screening Request', url('/tenant-screening'))
            ->line('Thank you for using NyumbaSmart!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $unit = $this->screening->unit;
        
        return [
            'id' => $this->screening->id,
            'type' => 'screening_request',
            'message' => 'You have received a new screening request for unit ' . $unit->unit_number,
            'unit_id' => $unit->id,
            'property_id' => $unit->property_id
        ];
    }
}