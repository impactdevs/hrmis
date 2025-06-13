<?php

namespace App\Notifications;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LeaveApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public Leave $leave;
    public User $user;
    public User $approver; // User who approved/rejected the leave

    /**
     * Create a new notification instance.
     */
    public function __construct(Leave $leave, User $approver)
    {
        $this->user = User::find($leave->user_id);
        $this->leave = $leave;
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
        if ($this->leave->leave_request_status === 'rejected') {
            return (new MailMessage)
                ->subject('Leave Application Rejected')
                ->line('Your leave application has been rejected.')
                ->line('Leave Type: ' . $this->leave->leaveCategory->leave_type_name)
                ->line('Start Date: ' . $this->leave->start_date->format('Y-m-d'))
                ->line('End Date: ' . $this->leave->end_date->format('Y-m-d'))
                ->line('Reason: ' . $this->leave->rejection_reason)
                ->line('Approved By: ' . $this->approver->name)
                ->action('View Leave Details', url('/leaves/' . $this->leave->id))
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->subject('Leave Application Approved')
                ->line('Your leave application has been approved.')
                ->line('Leave Type: ' . $this->leave->leaveCategory->leave_type_name)
                ->line('Start Date: ' . $this->leave->start_date->format('Y-m-d'))
                ->line('End Date: ' . $this->leave->end_date->format('Y-m-d'))
                ->line('Approved By: ' . $this->approver->name)
                ->action('View Leave Details', url('/leaves/' . $this->leave->id))
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
            'leave_id' => $this->leave->leave_id,
            'type' => $this->leave->leaveCategory->leave_type_name,
            'start_date' => $this->leave->start_date,
            'end_date' => $this->leave->end_date,
            'reason' => $this->leave->reason,
            'status' => $this->leave->leave_request_status,
            'message' => $this->leave->leave_request_status === 'rejected'
                ? 'Your leave application has been rejected.'
                : 'Your leave application has been approved.',
            'rejection_reason' => $this->leave->rejection_reason,
            'approved_by' => $this->approver->name, // Include who approved
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'leave_id' => $this->leave->leave_id,
            'type' => $this->leave->leave_type_id,
            'start_date' => $this->leave->start_date,
            'end_date' => $this->leave->end_date,
            'reason' => $this->leave->reason,
            'status' => $this->leave->leave_request_status,
            'user' => $this->user,
            'message' => $this->leave->leave_request_status === 'rejected'
                ? 'Your leave application has been rejected by ' . $this->approver->name . '.'
                : 'Your leave application has been approved by ' . $this->approver->name . '.',
            'rejection_reason' => $this->leave->rejection_reason,
            'approved_by' => $this->approver->name, // Include who approved
        ]);
    }
}
