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
            ->subject('Off-Desk Time Recorded')
            ->line('HR has recorded off-desk time for you.')
            ->line('Start: ' . $this->offDesk->start_datetime)
            ->line('End: ' . $this->offDesk->end_datetime)
            ->line('Destination: ' . $this->offDesk->destination)
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
            'message' => 'HR has recorded off-desk time for you (' . $this->offDesk->start_datetime . ' to ' . $this->offDesk->end_datetime . ').'
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'off_desk_id' => $this->offDesk->off_desk_id,
            'employee_first_name' => $this->name,
            'employee_last_name' => $this->last_name,
            'message' => 'HR has recorded off-desk time for you.'
        ]);
    }
}
