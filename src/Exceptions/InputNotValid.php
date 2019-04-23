<?php

namespace NotificationChannels\Textlocal\Exceptions;

use Exception;

class InputNotValid extends Exception
{
    public static function unacceptable($message, $code)
    {
        return new static("Input error: '{$message} [{$code}]");
    }
}
