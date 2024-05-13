<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SiteIsDown extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $secret)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Customize the subject line
        $subject = config('app.name') . " - Maintenance Mode Activated";

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting(new HtmlString('Hello Admin!'))
                    ->line("Your website is currently under maintenance. To access the admin panel, use the link below:")
                    ->action('Access Admin Panel', url($this->secret));
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
