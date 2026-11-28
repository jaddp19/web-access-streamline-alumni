<?php

namespace App\Exceptions;

use RuntimeException;

class OtpCooldownException extends RuntimeException
{
    public function __construct(public readonly int $retryAfter)
    {
        parent::__construct(
            "Please wait {$retryAfter} second(s) before requesting another verification code."
        );
    }
}
