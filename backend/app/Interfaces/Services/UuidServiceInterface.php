<?php

declare(strict_types=1);

namespace App\Interfaces\Services;

interface UuidServiceInterface
{
    public function generate(): string;
}
