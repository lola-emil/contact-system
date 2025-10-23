<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ContactShared implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sharer;
    public $receiverId;
    public $sharedContact;

    public function __construct(User $sharer, int $receiverId)
    {
        $this->sharer = $sharer;
        $this->receiverId =  $receiverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('share.contact.' . $this->receiverId),
        ];
    }

    public function broadcastWith(): array {
        return [
            "sharer" => $this->sharer,
        ];
    }
}
