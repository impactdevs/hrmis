<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BirthdayWish extends Notification implements ShouldQueue
{
    use Queueable;

    protected $employee;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Employee  $employee
     * @return void
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("Happy Birthday, {$this->employee->first_name}!")
                    ->line("May this special day bring you endless joy, laughter, and love. May your heart be filled with happiness, your dreams soar higher than ever, and your journey ahead be filled with success and adventure. You are truly one of a kind, and today is all about celebrating YOU!")
                    ->line('Wishing you a year ahead full of blessings, good health, and unforgettable moments. Enjoy every second of your day—you deserve it! 💖🎈✨')
                    ->line('UNCST MANAGEMENT');
    }

    /**
     * Get the database notification representation.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'employee_id' => $this->employee->employee_id,
            'employee_name' => $this->employee->first_name,
            'message' => "Today is the birthday of {$this->employee->first_name} {$this->employee->last_name}. Please wish them!"
        ]);
    }

    /**
     * Get the broadcast representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'employee_id' => $this->employee->employee_id,
            'employee_name' => $this->employee->first_name,
            'message' => "Today is the birthday of {$this->employee->first_name} {$this->employee->last_name}. Please wish them!"
        ]);
    }
}
