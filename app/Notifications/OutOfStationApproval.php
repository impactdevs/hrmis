<?php

namespace App\Notifications;

use App\Models\OutOfStationTraining;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OutOfStationApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public OutOfStationTraining $training;
    public User $user;
    public User $approver; // User who approved/rejected the leave
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(OutOfStationTraining $training, User $approver, $type)
    {
        $this->user = User::find($training->user_id);
        $this->training = $training;
        $this->approver = $approver; // Store the approver
        $this->type = $type;
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
        if ($this->type == 'rejected') {
            return (new MailMessage)
                ->subject('Travel Clearance Rejected')
                ->line('Your travel clearance has been rejected.')
                ->line('Destination: ' . $this->training->destination)
                ->line('Departure Date: ' . $this->training->departure_date->format('Y-m-d'))
                ->line('Return Date: ' . $this->training->return_date->format('Y-m-d'))
                ->line('Reason: ' . $this->training->rejection_reason)
                ->line('Rejected By: ' . $this->approver->name)
                ->action('View Training Details', url('/out-of-station-trainings/' . $this->training->training_id))
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->subject('Travel Clearance Approved')
                ->line('Your travel clearance has been approved.')
                ->line('Destination: ' . $this->training->destination)
                ->line('Departure Date: ' . $this->training->departure_date->format('Y-m-d'))
                ->line('Return Date: ' . $this->training->return_date->format('Y-m-d'))
                ->line('Approved By: ' . $this->approver->name)
                ->action('View Training Details', url('/trainings/' . $this->training->training_id))
                ->line('Thank you for using our application!');
        }
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
            'title' => $this->training->destination,
            'start_date' => $this->training->departure_date,
            'end_date' => $this->training->return_date,
            'status' => "approval",
            'message' => $this->user->name . ' requested for a training',
            'rejection_reason' => $this->training->rejection_reason,
            'approved_by' => $this->approver->name, // Include who approved
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
            'message' => $this->user->name . is_null($this->training->rejection_reason) ? ' approved a training' : ' rejected a training',
            'rejection_reason' => $this->training->rejection_reason,
            'approved_by' => $this->approver->name, // Include who approved
        ]);
    }
}
