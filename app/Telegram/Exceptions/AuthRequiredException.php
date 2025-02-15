<?php

namespace App\Telegram\Exceptions;

use RuntimeException;
use Throwable;

class AuthRequiredException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: 'Authorization by secret header required';

        parent::__construct($message, $code, $previous);
    }
}
