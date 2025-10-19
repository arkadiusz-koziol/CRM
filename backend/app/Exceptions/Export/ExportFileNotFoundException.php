<?php

declare(strict_types=1);

namespace App\Exceptions\Export;

use Symfony\Component\HttpFoundation\Response;

final class ExportFileNotFoundException extends ExportException
{
    public function __construct(string $filename)
    {
        parent::__construct("Export file not found: {$filename}");
    }

    public function getHttpStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
