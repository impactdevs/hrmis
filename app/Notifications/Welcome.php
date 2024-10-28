<?php

// app/Notifications/WelcomeEmployee.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Welcome extends Notification implements ShouldQueue
{
    use Queueable;

    protected $email;
    protected $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail']; // Send via email
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Welcome to the UNCST human resource management system!')
            ->line('We are excited to have you on board.')
            ->line('Your login credentials are:')
            ->line('Email: ' . $this->email)
            ->line('Password: ' . $this->password)
            ->action('Login to your account', url('/login'))
            ->line('Please change your password after logging in for the first time.')
            ->line('Thank you for joining us!');
    }
}
