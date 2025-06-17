<?php

namespace App\Notifications;

use App\Models\SalaryAdvance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;


class SalaryAdvanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public SalaryAdvance $salary_advance;
    public User $user;
    public User $approver; // User who approved/rejected the leave
    public $type;

    /**
     * Create a new notification instance.
     */
     public function __construct(SalaryAdvance $salary_advance, User $approver, $type)
    {
        $this->user = User::find($salary_advance->employee->user_id);
        $this->salary_advance = $salary_advance;
        $this->approver = $approver; // Store the approver
        $this->type = $type;
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
    if ($this->type == 'rejected') {
        return (new MailMessage)
            ->subject('Salary Advance Request Rejected')
            ->line('Your salary advance request has been rejected.')
            ->line('Amount Applied For: ' . number_format($this->salary_advance->amount_applied_for, 2))
            ->line('Reason: ' . $this->salary_advance->reasons)
            ->line('Repayment Start Date: ' . optional($this->salary_advance->repayment_start_date)->format('Y-m-d'))
            ->line('Repayment End Date: ' . optional($this->salary_advance->repayment_end_date)->format('Y-m-d'))
            ->line('Date of Contract Expiry: ' . optional($this->salary_advance->date_of_contract_expiry)->format('Y-m-d'))
            ->line('Net Monthly Pay: ' . number_format($this->salary_advance->net_monthly_pay, 2))
            ->line('Outstanding Loan: ' . number_format($this->salary_advance->outstanding_loan, 2))
            ->line('Comments: ' . $this->salary_advance->comments)
            ->line('Rejection Reason: ' . $this->salary_advance->rejection_reason)
            ->line('Processed By: ' . $this->approver->name)
            ->line('Thank you for using our application!');
    } else {
        return (new MailMessage)
            ->subject('Salary Advance Request Approved')
            ->line('Your salary advance request has been approved.')
            ->line('Amount Applied For: ' . number_format($this->salary_advance->amount_applied_for, 2))
            ->line('Reason: ' . $this->salary_advance->reasons)
            ->line('Repayment Start Date: ' . optional($this->salary_advance->repayment_start_date)->format('Y-m-d'))
            ->line('Repayment End Date: ' . optional($this->salary_advance->repayment_end_date)->format('Y-m-d'))
            ->line('Date of Contract Expiry: ' . optional($this->salary_advance->date_of_contract_expiry)->format('Y-m-d'))
            ->line('Net Monthly Pay: ' . number_format($this->salary_advance->net_monthly_pay, 2))
            ->line('Outstanding Loan: ' . number_format($this->salary_advance->outstanding_loan, 2))
            ->line('Comments: ' . $this->salary_advance->comments)
            ->line('Processed By: ' . $this->approver->name)
            ->line('Thank you for using our application!');
    }
}

public function toArray(object $notifiable): array
{
    return [
        'salary_advance_id' => $this->salary_advance->id,
        'amount_applied_for' => $this->salary_advance->amount_applied_for,
        'reasons' => $this->salary_advance->reasons,
        'repayment_start_date' => $this->salary_advance->repayment_start_date,
        'repayment_end_date' => $this->salary_advance->repayment_end_date,
        'date_of_contract_expiry' => $this->salary_advance->date_of_contract_expiry,
        'net_monthly_pay' => $this->salary_advance->net_monthly_pay,
        'outstanding_loan' => $this->salary_advance->outstanding_loan,
        'comments' => $this->salary_advance->comments,
        'loan_request_status' => $this->salary_advance->loan_request_status,
        'status' => $this->type,
        'message' => $this->user->name . ' requested a salary advance',
        'rejection_reason' => $this->salary_advance->rejection_reason,
        'processed_by' => $this->approver->name,
    ];
}

public function toBroadcast(object $notifiable): BroadcastMessage
{
    return new BroadcastMessage([
        'salary_advance_id' => $this->salary_advance->id,
        'amount_applied_for' => $this->salary_advance->amount_applied_for,
        'reasons' => $this->salary_advance->reasons,
        'repayment_start_date' => $this->salary_advance->repayment_start_date,
        'repayment_end_date' => $this->salary_advance->repayment_end_date,
        'date_of_contract_expiry' => $this->salary_advance->date_of_contract_expiry,
        'net_monthly_pay' => $this->salary_advance->net_monthly_pay,
        'outstanding_loan' => $this->salary_advance->outstanding_loan,
        'comments' => $this->salary_advance->comments,
        'loan_request_status' => $this->salary_advance->loan_request_status,
        'status' => $this->type,
        'user' => $this->user,
        'message' => $this->user->name . ($this->type == 'rejected' ? ' had their salary advance rejected' : ' had their salary advance approved'),
        'rejection_reason' => $this->salary_advance->rejection_reason,
        'processed_by' => $this->approver->name,
    ]);
}

}
