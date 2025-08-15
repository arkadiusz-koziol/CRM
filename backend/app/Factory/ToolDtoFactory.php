<?php

namespace App\Factory;

use App\Dto\ToolDto;

class ToolDtoFactory
{
    public static function fromRequest(
        string $name,
        string $description,
        int $count
    ): ToolDto {
        return new ToolDto(
            name: $name,
            description: $description,
            count: $count
        );
    }
}
