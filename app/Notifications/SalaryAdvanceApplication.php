<?php

namespace App\Notifications;

use App\Models\Appraisal;
use App\Models\SalaryAdvance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SalaryAdvanceApplication extends Notification implements ShouldQueue
{
    use Queueable;

    public SalaryAdvance $salaryAdvance;
    public string $first_name;
    public string $last_name;

    /**
     * Create a new notification instance.
     */
    public function __construct(SalaryAdvance $salaryAdvance, string $first_name, string $last_name)
    {
        $this->salaryAdvance = $salaryAdvance;
        $this->first_name = $first_name;
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
            ->subject('New Salary Advance Application')
            ->line('You have a new salary advance request with the following details.')
            ->line('Applicant: ' . $this->first_name . ' ' . $this->last_name)
            ->line('Amount Applied For: ' . $this->salaryAdvance->amount_applied_for)
            ->line('Check salary advance details: ' . url('/salary-advances/' . $this->salaryAdvance->id))
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
            'salary_advance_id' => $this->salaryAdvance->id,
            'applicant_first_name' => $this->first_name,
            'applicant_last_name' => $this->last_name,
            'amount_applied_for' => $this->salaryAdvance->amount_applied_for,
            'loan_request_status' => $this->salaryAdvance->loan_request_status,
            'message' => 'Salary Advance Application from ' . $this->first_name . ' ' . $this->last_name
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'salary_advance_id' => $this->salaryAdvance->id,
            'applicant_first_name' => $this->first_name,
            'applicant_last_name' => $this->last_name,
            'amount_applied_for' => $this->salaryAdvance->amount_applied_for,
            'loan_request_status' => $this->salaryAdvance->loan_request_status,
            'message' => 'Salary Advance Application from ' . $this->first_name . ' ' . $this->last_name
        ]);
    }
}
