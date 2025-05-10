<?php

namespace App\Notifications;

use App\Models\TenantScreening;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScreeningApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Tenant Screening Approved')
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line('Your tenant screening for the following unit has been approved:')
            ->line('Property: ' . $property->name)
            ->line('Unit: ' . $unit->unit_number)
            ->line('You can now proceed with the rental application process.')
            ->action('View Screening Details', url('/tenant-screening/' . $this->screening->id))
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
            'type' => 'screening_approved',
            'message' => 'Your tenant screening for unit ' . $unit->unit_number . ' has been approved',
            'unit_id' => $unit->id,
            'property_id' => $unit->property_id
        ];
    }
}