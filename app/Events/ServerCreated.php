<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ServerCreated implements ShouldBroadcast
{

    use SerializesModels;


    public function __construct(
        public User $user
    ) {}

    public function broadcastOn()
    {
        return [
            new PrivateChannel("user." . $this->user->id),
        ];
    }
}
