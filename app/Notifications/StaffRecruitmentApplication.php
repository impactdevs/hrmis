<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\StaffRecruitment;

class StaffRecruitmentApplication extends Notification implements ShouldQueue
{
    use Queueable;

    public StaffRecruitment $staffRecrutment;

    /**
     * Create a new notification instance.
     */
    public function __construct(StaffRecruitment $staffRecrutment)
    {
        $this->staffRecrutment = $staffRecrutment;
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
            ->subject('New Staff Recruitment Application')
            ->line('We need new staff recruited')
            ->line('Staff Recruitment Position: ' . $this->staffRecrutment->position)
            ->line('Staff Recruitment Needed By: ' . $this->staffRecrutment->date_of_recruitment->format('Y-m-d'))
            ->line('Justification: ' . $this->staffRecrutment->justification)
            ->action('View Staff Recruitment Details', url('/recruitments/' . $this->staffRecrutment->staff_recruitment_id))
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
            'staff_recruitment_id' => $this->staffRecrutment->staff_recruitment_id,
            'title' => $this->staffRecrutment->position,
            'start_date' => $this->staffRecrutment->date_of_recruitment,
            'description' => $this->staffRecrutment->training_description,
            'message' => 'Recruitment Request: ' . $this->staffRecrutment->training_title
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'staff_recruitment_id' => $this->staffRecrutment->staff_recruitment_id,
            'title' => $this->staffRecrutment->position,
            'start_date' => $this->staffRecrutment->date_of_recruitment,
            'description' => $this->staffRecrutment->training_description,
            'message' => 'Recruitment Request: ' . $this->staffRecrutment->training_title
        ]);
    }
}
