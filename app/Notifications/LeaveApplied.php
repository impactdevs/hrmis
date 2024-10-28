<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LeaveApplied extends Notification implements ShouldQueue
{
    use Queueable;

    public Leave $leave;

    public User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(Leave $leave)
    {
        $this->user = User::find($leave->user_id);
        $this->leave = $leave;
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
            ->subject('Leave Application Submitted')
            ->line('You have a new leave application.')
            ->line('Leave Type: ' . $this->leave->leaveCategory->leave_type_name)
            ->line('Start Date: ' . $this->leave->start_date->format('Y-m-d'))
            ->line('End Date: ' . $this->leave->end_date->format('Y-m-d'))
            ->line('Reason: ' . $this->leave->reason)
            ->action('View Leave Details', url('/leaves/' . $this->leave->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'leave_id' => $this->leave->leave_id,
            'type' => $this->leave->leaveCategory->leave_type_name,
            'start_date' => $this->leave->start_date,
            'end_date' => $this->leave->end_date,
            'reason' => $this->leave->reason,
            'message' => $this->user->name . ' requested for a leave',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {

        return new BroadcastMessage([
            'leave_id' => $this->leave->leave_id,
            'type' => $this->leave->leaveCategory->leave_type_name,
            'start_date' => $this->leave->start_date,
            'end_date' => $this->leave->end_date,
            'reason' => $this->leave->reason,
            'user' => $this->user,
            'message' => $this->user->name . ' requested for a leave',
        ]);
    }
}
