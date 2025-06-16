<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AppraisalDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;

    public $contract;

    /**
     * Create a new notification instance.
     */
    public function __construct($type, $contract=null)
    {
        $this->type = $type;
        $this->contract = $contract;
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
        if ($this->type == 'mid_financial_year') {
            return (new MailMessage)
                ->subject('Appraisal Application Due for financial year ' . (date('Y') - 1). '-'.date('Y'))
                ->line('Hello!,')
                ->line('Your appraisal application is due.')
                ->action('Follow the link to apply for your appraisal', url('/uncst-appraisals'))
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->subject('Contract Expiry appraisal Due')
                ->line('Hello!,')
                ->line('You are reminded to do an appraisal for your contract expirey with the following details.')
                ->line('Start Date: '.$this->contract->start_date.' '.'End Date: '.$this->contract->end_date)
                ->line('Description: '.$this->contract->description)
                ->action('For more details', url('/contract/'.$this->contract->id.'/show'))
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
        if ($this->type == 'mid_financial_year') {
            return [
                'reminder_category' => "appraisal",
                'message' => 'Appraisal Application Due for financial year ' . (date('Y') - 1) . '-' . date('Y')
            ];
        } else {
            return [
                'reminder_category' => "appraisal",
                'message' => 'Contract Expiry appraisal Due for contract: ' . ($this->contract ? $this->contract->description : '')
            ];
        }
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        if ($this->type == 'mid_financial_year') {
            return new BroadcastMessage([
                'reminder_category' => "appraisal",
                'message' => 'Appraisal Application Due for financial year ' . (date('Y') - 1) . '-' . date('Y')
            ]);
        } else {
            return new BroadcastMessage([
                'reminder_category' => "appraisal",
                'message' => 'Contract Expiry appraisal Due for contract: ' . ($this->contract ? $this->contract->description : '')
            ]);
        }
    }
}
