<?php

namespace App\Notifications;

use App\Models\WhistleblowingReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class WhistleBlowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public WhistleblowingReport $report;

    /**
     * Create a new notification instance.
     */
    public function __construct(WhistleblowingReport $report)
    {
        $this->report = $report;
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
            ->line('There is a new whistleblowing report submitted.')
            ->action('Notification Action', url('/whistleblowing/' . $this->report->id))
            ->line('Thank you for using our application!')
            ->subject('UNCST HRMIS - New Whistleblowing Report');
    }
 /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'message' => 'A new whistleblowing report has been submitted.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'report_id' => $this->report->id,
            'message' => 'A new whistleblowing report has been submitted.',
        ]);
    }
}
