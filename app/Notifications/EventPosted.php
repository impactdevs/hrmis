<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EventPosted extends Notification implements ShouldQueue
{
    use Queueable;

    public Event $event;


    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
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
            ->subject('New Event Posted')
            ->line('A new event is available for you to attend.')
            ->line('Event Title: ' . $this->event->event_title)
            ->line('Start Date: ' . $this->event->event_start_date->format('Y-m-d'))
            ->line('End Date: ' . $this->event->event_end_date->format('Y-m-d'))
            ->line('Location: ' . $this->event->event_location)
            ->line('Description: ' . $this->event->event_description)
            ->action('View Event Details', url('/events/' . $this->event->event_id))
            ->line('We hope to see you there!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->event_id,
            'title' => $this->event->event_title,
            'event_start_date' => $this->event->event_start_date->format('Y-m-d'),
            'event_end_date' => $this->event->event_end_date->format('Y-m-d'),
            'location' => $this->event->event_location,
            'description' => $this->event->event_description,
            'message' => 'Event Invitation: ' . $this->event->event_title
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'event_id' => $this->event->event_id,
            'title' => $this->event->event_title,
            'event_start_date' => $this->event->event_start_date->format('Y-m-d'),
            'event_end_date' => $this->event->event_end_date->format('Y-m-d'),
            'location' => $this->event->event_location,
            'description' => $this->event->event_description,
            'message' => 'Event Invitation: ' . $this->event->event_title
        ]);
    }
}
