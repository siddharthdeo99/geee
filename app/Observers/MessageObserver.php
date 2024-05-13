<?php

namespace App\Observers;

use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\User\MessageSentNotification;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message)
    {
        $existingMessagesCount = Message::where('conversation_id', $message->conversation_id)->count() - 1; // Subtract 1 to exclude the current message

        $conversation = Conversation::find($message->conversation_id);
        $buyerName = $conversation->buyer->name;
        $productName = $conversation->ad->title;
        // Trigger notification to the receiver
        $receiver = $message->receiver;
        $receiver->notify(new MessageSentNotification($message, $existingMessagesCount, $buyerName, $productName));
    }
}
