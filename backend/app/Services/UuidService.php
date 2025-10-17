<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Services\UuidServiceInterface;
use Ramsey\Uuid\Uuid;

final class UuidService implements UuidServiceInterface
{
    public function generate(): string
    {
        return Uuid::uuid7()->toString();
    }
}
