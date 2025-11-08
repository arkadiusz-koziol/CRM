<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\UsableCountDto;

final class UsableCountDtoFactory
{
    public function create(string $entity, int $count): UsableCountDto
    {
        return new UsableCountDto(
            entity: $entity,
            count: $count
        );
    }
}

