<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class UsableCountUnavailableException extends Exception
{
    public function __construct(
        string $entity,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct(
            "Usable count could not be determined for {$entity} at this time.",
            $code,
            $previous
        );
    }
}
