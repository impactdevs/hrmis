<?php

namespace App\Notifications;
use App\Models\StaffRecruitment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class StaffRecruitmentApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public StaffRecruitment $staffRecrutment;
    public User $user;
    public User $approver; // User who approved/rejected the request
    public string $decision; // 'approved' or 'rejected'

    /**
     * Create a new notification instance.
     */
    public function __construct(StaffRecruitment $staffRecrutment, User $approver, string $decision)
    {
        $this->user = User::find($staffRecrutment->user_id);
        $this->staffRecrutment = $staffRecrutment;
        $this->approver = $approver;
        $this->decision = $decision;
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
        if ($this->decision === 'rejected') {
            return (new MailMessage)
                ->subject('Staff Recruitment Application Rejected')
                ->line('Your staff recruitment application has been rejected.')
                ->line('Staff Recruitment Position: ' . $this->staffRecrutment->position)
                ->line('Staff Recruitment Needed By: ' . $this->staffRecrutment->date_of_recruitment->format('Y-m-d'))
                ->line('Justification: ' . $this->staffRecrutment->justification)
                ->line('Rejected By: ' . $this->approver->name)
                ->action('View Staff Recruitment Details', url('/recruitments/' . $this->staffRecrutment->staff_recruitment_id))
                ->line('Thank you for using our application!');
        }

        return (new MailMessage)
            ->subject('Staff Recruitment Application Approved')
            ->line('Your staff recruitment application has been approved.')
            ->line('Staff Recruitment Position: ' . $this->staffRecrutment->position)
            ->line('Staff Recruitment Needed By: ' . $this->staffRecrutment->date_of_recruitment->format('Y-m-d'))
            ->line('Justification: ' . $this->staffRecrutment->justification)
            ->line('Approved By: ' . $this->approver->name)
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
            'status' => $this->decision,
            'message' => 'Your staff recruitment request for "' . $this->staffRecrutment->position . '" was ' . $this->decision,
            'rejection_reason' => $this->staffRecrutment->rejection_reason,
            'approved_by' => $this->approver->name, // Include who approved
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
            'status' => $this->decision,
            'message' => 'Your staff recruitment request for "' . $this->staffRecrutment->position . '" was ' . $this->decision,
            'rejection_reason' => $this->staffRecrutment->rejection_reason,
            'approved_by' => $this->approver->name, // Include who approved
        ]);
    }
}
