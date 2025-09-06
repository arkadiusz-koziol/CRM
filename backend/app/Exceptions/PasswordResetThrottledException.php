<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class PasswordResetThrottledException extends Exception
{
    public function __construct(
        string $message = 'Password reset throttled',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
