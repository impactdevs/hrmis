<?php

namespace App\Notifications;

use App\Models\WorkFromHome;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class WorkFromHomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public WorkFromHome $workFromHome;
    public string $name;
    public string $last_name;
    /**
     * Create a new notification instance.
     */
    public function __construct(
        WorkFromHome $workFromHome,
        string $name,
        string $last_name
    ) {
        $this->workFromHome = $workFromHome;
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
            ->subject('Work From Home Scheduled')
            ->line('HR has scheduled a work-from-home period for you.')
            ->line('Start Date: ' . $this->workFromHome->work_from_home_start_date)
            ->line('End Date: ' . $this->workFromHome->work_from_home_end_date)
            ->line('Reason: ' . $this->workFromHome->work_from_home_reason);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'work_from_home_id' => $this->workFromHome->work_from_home_id,
            'employee_first_name' => $this->name,
            'employee_last_name' => $this->last_name,
            'reason' => $this->workFromHome->work_from_home_reason,
            'message' => 'HR has scheduled a work-from-home period for you (' . $this->workFromHome->work_from_home_start_date . ' to ' . $this->workFromHome->work_from_home_end_date . ').'
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'work_from_home_id' => $this->workFromHome->work_from_home_id,
            'employee_first_name' => $this->name,
            'employee_last_name' => $this->last_name,
            'message' => 'HR has scheduled a work-from-home period for you.',
        ]);
    }
}
