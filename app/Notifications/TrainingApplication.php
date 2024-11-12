<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TrainingApplication extends Notification implements ShouldQueue
{
    use Queueable;

    public Training $training;

    /**
     * Create a new notification instance.
     */
    public function __construct(Training $training)
    {
        $this->training = $training;
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
            ->subject('New Application')
            ->line('A new training opportunity is available.')
            ->line('Training Topic: ' . $this->training->training_title)
            ->line('Start Date: ' . $this->training->training_start_date->format('Y-m-d'))
            ->line('End Date: ' . $this->training->training_end_date->format('Y-m-d'))
            ->line('Description: ' . $this->training->training_description)
            ->line('Location: ' . $this->training->training_location)
            ->action('View Training Details', url('/trainings/' . $this->training->training_id))
            ->line('Thank you for your help in professional development!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'training_id' => $this->training->training_id,
            'topic' => $this->training->training_title,
            'start_date' => $this->training->training_start_date,
            'end_date' => $this->training->training_end_date,
            'description' => $this->training->training_description,
            'message' => 'Training Invitation: ' . $this->training->training_title
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'training_id' => $this->training->training_id,
            'topic' => $this->training->training_title,
            'start_date' => $this->training->training_start_date,
            'end_date' => $this->training->training_end_date,
            'description' => $this->training->training_description,
            'message' => 'Training Invitation: ' . $this->training->training_title
        ]);
    }
}
