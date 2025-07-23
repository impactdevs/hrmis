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
            ->line('A new work from home request has been submitted.')
            ->line('Employee: ' . $this->name . ' ' . $this->last_name)
            ->line('Start Date: ' . $this->workFromHome->start_date)
            ->line('End Date: ' . $this->workFromHome->end_date)
            ->line('Reason: ' . $this->workFromHome->reason)
            ->subject('Work From Home Request');
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
            'reason' => $this->workFromHome->reason,
            'message' => 'A new Work From home Application from ' . $this->name . ' ' . $this->last_name
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'work_from_home_id' => $this->workFromHome->work_from_home_id,
            'employee_first_name' => $this->name,
            'employee_last_name' => $this->last_name,
            'message' => 'A new work from home request has been submitted.',
        ]);
    }
}
