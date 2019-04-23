<?php

namespace NotificationChannels\Textlocal;

use Illuminate\Notifications\Notification;

class TextlocalChannel
{
    public function __construct(TextlocalClient $client)
    {
        $this->textlocal = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Textlocal\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('textlocal')) {
            return;
        }

        $message = $notification->toTextlocal($notifiable);

        if (is_string($message)) {
            $message = new TextlocalMessage($message);
        }

        return $this->textlocal->message($to, $message);
    }
}
