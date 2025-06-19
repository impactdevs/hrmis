<?php

namespace App\Notifications;

use App\Models\Leave;
use App\Models\LeaveRoster;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApplicationDueReminder extends Notification
{
    use Queueable;

    public $leaveRoster;

    /**
     * Create a new notification instance.
     */
    public function __construct(LeaveRoster $leaveRoster)
    {
        $this->leaveRoster = $leaveRoster;
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
            ->subject('You have an Annual Leave Application Due')
            ->line('Dear ' . $notifiable->name . ',')
            ->line('This is a reminder that you have an annual leave application due soon.')
            ->line('Leave Start Date: ' . $this->leaveRoster->start_date->format('Y-m-d'))
            ->line('Leave End Date: ' . $this->leaveRoster->end_date->format('Y-m-d'))
            ->line('Please ensure that you submit your leave application before the due date.')
            ->action('Apply here:', url('/apply-for-leave/' . $this->leaveRoster->leave_roster_id))
            ->line('Thank you for your attention!')
            ->line('Best regards,')
            ->line('Your HR Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'leave_roster_id' => $this->leaveRoster->leave_roster_id,
            'start_date' => $this->leaveRoster->start_date->format('Y-m-d'),
            'end_date' => $this->leaveRoster->end_date->format('Y-m-d'),
            'message' => 'You have an annual leave application due soon.'
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'leave_roster_id' => $this->leaveRoster->leave_roster_id,
            'start_date' => $this->leaveRoster->start_date->format('Y-m-d'),
            'end_date' => $this->leaveRoster->end_date->format('Y-m-d'),
            'message' => 'You have an annual leave application due soon.'
        ]);
    }
}
