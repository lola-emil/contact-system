<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('channel-name', function ($user) {
    // You can adjust the condition to check if the user is authorized
    return true; // Replace with actual authorization logic
});

Broadcast::channel('notify.{id}', function ($user, $id) {

    Log::info('Authenticated User:', ['user' => $user]);
    Log::info('Requested Channel UserID:', ['requested_user_id' => $id]);

    return $user->id === (int) $id;
});

Broadcast::routes(["middleware" => ["auth", "web"]]);
