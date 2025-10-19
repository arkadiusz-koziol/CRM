<?php

declare(strict_types=1);

namespace App\Exceptions\Export;

use Exception;

abstract class ExportException extends Exception
{
    abstract public function getHttpStatusCode(): int;
}
