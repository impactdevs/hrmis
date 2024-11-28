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

    /**
     * Create a new notification instance.
     */
    public function __construct(Appraisal $appraisal, string $name, string $last_name)
    {
        $this->appraisal = $appraisal;
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
            //if approval status is reject, let the subject be Appraisal Rejected else let it be Appraisal Approved
            ->subject($this->appraisal->status == 'Rejected' ? 'Appraisal Rejected' : 'Appraisal Approved')
            ->line('You have an appraisal with the following details.')
            ->line('Appraisal ID: ' . $this->appraisal->appraisal_id)
            ->line('Appraisal Status: ' . $this->appraisal->status)
            ->line('Check appraisal details: ' . url('/appraisals/' . $this->appraisal->appraisal_id))
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
            'appraisal_id' => $this->appraisal->appraisal_id,
            'appraisee_first_name' => $this->name,
            'appraisee_last_name' => $this->last_name,
            'message' =>$this->appraisal->status == 'Rejected' ? 'Appraisal Rejected' : 'Appraisal Approved by '.$this->name.' '.$this->last_name
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'appraisal_id' => $this->appraisal->appraisal_id,
            'appraisee_first_name' => $this->name,
            'appraisee_last_name' => $this->last_name,
            'message' => $this->appraisal->status == 'Rejected' ? 'Appraisal Rejected' : 'Appraisal Approved by '. $this->name.' '.$this->last_name
        ]);
    }
}
