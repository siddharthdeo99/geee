<?php

namespace App\Notifications\User;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class MessageSentNotification extends Notification
{
    use Queueable;

    protected $message, $existingMessagesCount, $buyerName, $productName;

    public function __construct(Message $message, $existingMessagesCount, $buyerName, $productName)
    {
        $this->message = $message;
        $this->existingMessagesCount = $existingMessagesCount;
        $this->buyerName = $buyerName;
        $this->productName = $productName;
    }


    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $notificationTitle = $this->existingMessagesCount == 0 ?
            __('messages.t_new_interest') :
            __('messages.t_new_message');

        $notificationBody = $this->existingMessagesCount == 0 ?
            __('messages.t_received_new_interest', ['buyerName' => $this->buyerName, 'productName' => $this->productName]) :
            __('messages.t_follow_up_message', ['buyerName' => $this->buyerName, 'productName' => $this->productName]);

        $url = url("/my-messages?conversation_id={$this->message->conversation_id}");
        return (new MailMessage)
                    ->subject($notificationTitle)
                    ->line($notificationBody)
                    ->action('View Message', $url);
    }

   public function toDatabase($notifiable): array
    {
        $url = url("/my-messages?conversation_id={$this->message->conversation_id}");

        $notificationTitle = $this->existingMessagesCount == 0 ?
            __('messages.t_new_interest') :
            __('messages.t_new_message');

        $notificationBody = $this->existingMessagesCount == 0 ?
            __('messages.t_received_new_interest', ['buyerName' => $this->buyerName, 'productName' => $this->productName]) :
            __('messages.t_follow_up_message', ['buyerName' => $this->buyerName, 'productName' => $this->productName]);


        return FilamentNotification::make()
        ->success()
        ->title($notificationTitle)
        ->body($notificationBody)
        ->actions([
            Action::make('view')
                ->button()
                ->markAsRead()
                ->url(fn(): string =>  $url)
        ])
        ->getDatabaseMessage();
    }

}
