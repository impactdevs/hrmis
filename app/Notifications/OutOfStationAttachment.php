<?php

namespace App\Notifications;

use App\Models\OutOfStationTraining;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OutOfStationAttachment extends Notification implements ShouldQueue
{
    use Queueable;

    public OutOfStationTraining $training;
    public User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(OutOfStationTraining $training)
    {
        $this->user = User::find($training->user_id);
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
                ->subject('You have been requested to be a substitute of someone on travel')
                ->line('The following are the details of the person\'s travel')
                ->line('Destination: ' . $this->training->destination)
                ->line('Departure Date: ' . $this->training->departure_date->format('Y-m-d'))
                ->line('Return Date: ' . $this->training->return_date->format('Y-m-d'))
                ->action('View Training Details', url('/out-of-station-trainings/' . $this->training->training_id))
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
            'travel_training_id' => $this->training->training_id,
            'title' => $this->training->destination,
            'start_date' => $this->training->departure_date,
            'end_date' => $this->training->return_date,
            'status' => "approval",
            'message' => $this->user->name . ' attached you as their substitute while away',
            'rejection_reason' => $this->training->rejection_reason 
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'leave_id' => $this->training->leave_id,
            'type' => $this->training->leave_type_id,
            'start_date' => $this->training->start_date,
            'end_date' => $this->training->end_date,
            'status' => "approval",
            'user' => $this->user,
             'message' => $this->user->name . ' attached you as their substitute while away',
            'rejection_reason' => $this->training->rejection_reason
        ]);
    }
}
