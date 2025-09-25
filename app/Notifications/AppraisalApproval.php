<?php

namespace App\Notifications;

use App\Models\Appraisal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AppraisalApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public Appraisal $appraisal;

    public string $name;
    public string $last_name;
    protected $status;
    protected $approverName;
    protected $approverRole;
    protected $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appraisal $appraisal, string $name, string $last_name, $status = null, $approverName = null, $approverRole = null, $rejectionReason = null)
    {
        $this->appraisal = $appraisal;
        $this->name = $name;
        $this->last_name = $last_name;
        $this->status = $status ?? $appraisal->approval_status;
        $this->approverName = $approverName ?? auth()->user()->name;
        $this->approverRole = $approverRole ?? auth()->user()->getRoleNames()->first();
        $this->rejectionReason = $rejectionReason ?? $appraisal->rejection_reason;
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
        $status = $this->status ?? $this->appraisal->approval_status;

        return (new MailMessage)
            ->subject($status == 'rejected' ? 'Appraisal Rejected' : 'Appraisal Approved')
            ->line('You have an appraisal with the following details.')
            ->line('Appraisal ID: ' . $this->appraisal->appraisal_id)
            ->line('Appraisal Status: ' . ucfirst($status))
            ->line('Check appraisal details: ' . url('/appraisals/' . $this->appraisal->appraisal_id))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->status ?? $this->appraisal->approval_status;

        return [
            'appraisal_id' => $this->appraisal->appraisal_id,
            'appraisee_first_name' => $this->name,
            'appraisee_last_name' => $this->last_name,
            'approver_role' => $this->approverRole,
            'message' => $status == 'rejected' ?
                'Appraisal Rejected by ' . $this->approverName . ' (' . $this->approverRole . ')' :
                'Appraisal Approved by ' . $this->approverName . ' (' . $this->approverRole . ')'
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $status = $this->status ?? $this->appraisal->approval_status;

        return new BroadcastMessage([
            'appraisal_id' => $this->appraisal->appraisal_id,
            'appraisee_first_name' => $this->name,
            'appraisee_last_name' => $this->last_name,
            'message' => $status == 'rejected' ?
                'Appraisal Rejected by ' . $this->approverName . ' (' . $this->approverRole . ')' :
                'Appraisal Approved by ' . $this->approverName . ' (' . $this->approverRole . ')'
        ]);
    }
}
