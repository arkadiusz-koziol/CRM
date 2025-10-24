<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class OpportunityNotFoundException extends \RuntimeException
{
    public function __construct(string $message = 'Opportunity not found.')
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND);
    }
}
