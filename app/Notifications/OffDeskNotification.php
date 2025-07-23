<?php

namespace App\Notifications;

use App\Models\OffDesk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;


class OffDeskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public OffDesk $offDesk;
    public string $name;
    public string $last_name;

    /**
     * Create a new notification instance.
     */
    public function __construct(OffDesk $offDesk, string $name, string $last_name)
    {
        $this->offDesk = $offDesk;
        $this->name = $name;
        $this->last_name = $last_name;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Off-Desk Request')
            ->line('A new off-desk request has been submitted.')
            ->line('Employee: ' . $this->name . ' ' . $this->last_name)
            ->line('Start Date: ' . $this->offDesk->start_date)
            ->line('End Date: ' . $this->offDesk->end_date)
            ->line('Reason: ' . $this->offDesk->reason);
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'off_desk_id' => $this->offDesk->off_desk_id,
            'employee_first_name' => $this->name,
            'employee_last_name' => $this->last_name,
            'reason' => $this->offDesk->reason,
            'message' => 'A new Off Desk Application from ' . $this->name . ' ' . $this->last_name
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'off_desk_id' => $this->offDesk->id,
            'employee_first_name' => $this->name,
            'employee_last_name' => $this->last_name,
            'message' => 'New Off-Desk Request from ' . $this->name . ' ' . $this->last_name
        ]);
    }
}
