<?php

namespace NotificationChannels\Textlocal\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError($error)
    {
        return new static("Textlocal responded with error: '{$error->message} [{$error->code}]'");
    }
}
