<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class BirthdayReminderForEmployee extends Mailable
{
    public $employee;

    public function __construct(User $employee)
    {
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('Happy Birthday!')
                    ->view('emails.birthday_reminder_employee');
    }
}
