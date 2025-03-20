<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RosterUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $employee;
    public $total_leave_days_entitled;
    public $total_leave_days_scheduled;

    /**
     * Create a new event instance.
     */
    public function __construct($employee, $total_leave_days_entitled, $total_leave_days_scheduled)
    {
        $this->employee = $employee;
        $this->total_leave_days_entitled = $total_leave_days_entitled;
        $this->total_leave_days_scheduled = $total_leave_days_scheduled;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("roster.{$this->employee}"),
        ];
    }

    public function broadcastWith(): array
    {
        //broadcast certificate data
        return [
            'total_leave_days_entitled' => $this->total_leave_days_entitled,
            'total_leave_days_scheduled' => $this->total_leave_days_scheduled
        ];
    }
}

