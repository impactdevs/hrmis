<?php

namespace App\Notifications;

use App\Models\Appraisal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppraisalResubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $appraisal;
    public $firstName;
    public $lastName;

    public function __construct(Appraisal $appraisal, $firstName, $lastName)
    {
        $this->appraisal = $appraisal;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Appraisal Resubmitted')
            ->line("The appraisal for {$this->firstName} {$this->lastName} has been resubmitted after addressing rejection feedback.")
            ->action('Review Appraisal', route('uncst-appraisals.edit', $this->appraisal->appraisal_id))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'appraisal_id' => $this->appraisal->appraisal_id,
            'message' => "{$this->firstName} {$this->lastName} has resubmitted their appraisal for review.",
            'link' => route('uncst-appraisals.edit', $this->appraisal->appraisal_id),
        ];
    }
}