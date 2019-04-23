<?php

namespace NotificationChannels\Textlocal\Exceptions;

use Exception;

class CouldNotAuthenticateAccount extends Exception
{
    public static function apiKeyMissing($accountType)
    {
        return new static(
            "No record found for config('services.textlocal.{$accountType}.apiKey')"
        );
    }
}
