<?php

namespace App\Notifications;

use App\Models\TenantScreening;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScreeningRejectedNotification extends Notification implements ShouldQueue
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
        $reportData = json_decode($this->screening->report_data, true);
        $reason = $reportData['rejection_info']['reason'] ?? 'No specific reason provided';
        
        return (new MailMessage)
            ->subject('Tenant Screening Result')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We regret to inform you that your tenant screening application for the following unit was not approved:')
            ->line('Property: ' . $property->name)
            ->line('Unit: ' . $unit->unit_number)
            ->line('Reason: ' . $reason)
            ->line('If you have any questions or would like to discuss this further, please contact the property manager.')
            ->action('View Screening Details', url('/tenant-screening/' . $this->screening->id))
            ->line('Thank you for using NyumbaSmart.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $unit = $this->screening->unit;
        $reportData = json_decode($this->screening->report_data, true);
        $reason = $reportData['rejection_info']['reason'] ?? 'No specific reason provided';
        
        return [
            'id' => $this->screening->id,
            'type' => 'screening_rejected',
            'message' => 'Your tenant screening for unit ' . $unit->unit_number . ' was not approved',
            'reason' => $reason,
            'unit_id' => $unit->id,
            'property_id' => $unit->property_id
        ];
    }
}