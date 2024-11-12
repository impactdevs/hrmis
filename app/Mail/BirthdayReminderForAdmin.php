<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class BirthdayReminderForAdmin extends Mailable
{
    public $employee;

    public function __construct(User $employee)
    {
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('Birthday Reminder for Tomorrow')
                    ->view('emails.birthday_reminder_admin');
    }
}
