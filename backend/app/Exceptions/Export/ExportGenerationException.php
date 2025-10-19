<?php

declare(strict_types=1);

namespace App\Exceptions\Export;

use Symfony\Component\HttpFoundation\Response;

final class ExportGenerationException extends ExportException
{
    public function __construct(string $format, string $reason)
    {
        parent::__construct("Failed to generate {$format} export: {$reason}");
    }

    public function getHttpStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
