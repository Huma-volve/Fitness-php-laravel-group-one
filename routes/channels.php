<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = \App\Models\Conversation::findOrFail($id);

    return $user->id === $conversation->user_id ||
           $user->id === $conversation->trainer_id;
});