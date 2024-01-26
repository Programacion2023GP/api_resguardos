<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Events
{
    use SerializesModels;

    public $message;
    public $nombreDelCanal;

    public function __construct($message, $nombreDelCanal)
    {
        $this->message = $message;
        $this->nombreDelCanal = $nombreDelCanal;
    }

    public function broadcastOn()
    {
        return new Channel($this->nombreDelCanal);
    }
}
