<?php

namespace App\Notifications;

use App\Models\Leave;
use App\Models\Training;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TrainingApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public Training $training;
    public User $user;
    public User $approver; // User who approved/rejected the leave

    /**
     * Create a new notification instance.
     */
    public function __construct(Training $training, User $approver)
    {
        $this->user = User::find($training->user_id);
        $this->training = $training;
        $this->approver = $approver; // Store the approver
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
        if ($this->training->approval_status === 'rejected') {
            return (new MailMessage)
                ->subject('Training Application Rejected')
                ->line('Your training application has been rejected.')
                ->line('Training Topic: ' . $this->training->training_title)
                ->line('Training Start Date: ' . $this->training->training_start_date->format('Y-m-d'))
                ->line('Training End Date: ' . $this->training->training_end_date->format('Y-m-d'))
                ->line('Reason: ' . $this->training->rejection_reason)
                ->line('Approved By: ' . $this->approver->name)
                ->action('View Training Details', url('/trainings/' . $this->training->training_id))
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->subject('Training Application Approved')
                ->line('Your training application has been approved.')
                ->line('Training Topic: ' . $this->training->training_title)
                ->line('Training Start Date: ' . $this->training->training_start_date->format('Y-m-d'))
                ->line('Training End Date: ' . $this->training->training_end_date->format('Y-m-d'))
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
            'title' => $this->training->training_title,
            'start_date' => $this->training->training_start_date,
            'end_date' => $this->training->training_end_date,
            'status' => $this->training->approval_status,
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
            'status' => $this->training->approval_status,
            'user' => $this->user,
            'message' => $this->user->name . is_null($this->training->rejection_reason) ? ' approved a training' : ' rejected a training',
            'rejection_reason' => $this->training->rejection_reason,
            'approved_by' => $this->approver->name, // Include who approved
        ]);
    }
}
