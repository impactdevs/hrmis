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
    public string $user_category;
    public Employee $employee;

    /**
     * Create a new notification instance.
     */
    public function __construct(Leave $leave, $user_category)
    {
        $this->user = User::find($leave->user_id);
        $this->leave = $leave;
        $this->user_category = $user_category;
        $this->employee = auth()->user()->employee;
    }

    /**
     * Get the notification's delivery channels.
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
        $mail = new MailMessage();

        switch ($this->user_category) {
            case 1:
                $subject = "You're Covering for " . $this->employee->first_name;
                $message = "Hello, you have been designated as a stand-in for " . $this->employee->first_name . " " . $this->employee->last_name . " while they are on leave.";
                break;
            case 2:
                $subject = "New Leave Application Submitted";
                $message = "A new leave application has been submitted and requires your review.";
                break;
            case 3:
                $subject = "Leave Request Notification";
                $message = "You have received a new leave application.";
                break;
            default:
                $subject = "Leave Notification";
                $message = "A new leave request has been submitted.";
        }

        return $mail
            ->subject($subject)
            ->greeting("Hello!")
            ->line($message)
            ->line("**Leave Type:** " . $this->leave->leaveCategory->leave_type_name)
            ->line("**Start Date:** " . $this->leave->start_date->format('Y-m-d'))
            ->line("**End Date:** " . $this->leave->end_date->format('Y-m-d'))
            ->line("**Reason:** " . $this->leave->reason)
            ->action("View Leave Details", url('/leaves/' . $this->leave->id))
            ->line("Thank you for staying updated!");
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'leave_id' => $this->leave->id,
            'type' => $this->leave->leaveCategory->leave_type_name,
            'start_date' => $this->leave->start_date,
            'end_date' => $this->leave->end_date,
            'reason' => $this->leave->reason,
            'message' => $this->generateNotificationMessage()
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'leave_id' => $this->leave->id,
            'type' => $this->leave->leaveCategory->leave_type_name,
            'start_date' => $this->leave->start_date,
            'end_date' => $this->leave->end_date,
            'reason' => $this->leave->reason,
            'user' => $this->user,
            'message' => $this->generateNotificationMessage()
        ]);
    }

    /**
     * Generate notification message based on the user category.
     */
    private function generateNotificationMessage(): string
    {
        switch ($this->user_category) {
            case 1:
                return $this->user->name . " has assigned you as their stand-in while on leave.";
            case 2:
                return "A new leave application has been submitted for approval.";
            case 3:
                return "A leave request has been submitted.";
            default:
                return "A leave request has been received.";
        }
    }
}
