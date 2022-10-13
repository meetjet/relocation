<?php

namespace App\Telegram\Exceptions;

use RuntimeException;
use Throwable;

class UserRequiredException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: 'User required';

        parent::__construct($message, $code, $previous);
    }
}
