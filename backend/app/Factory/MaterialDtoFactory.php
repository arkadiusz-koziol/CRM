<?php

namespace App\Factory;

use App\Dto\MaterialDto;

class MaterialDtoFactory
{
    public static function fromRequest(
        string $name,
        string $description,
        int $count,
        int $price
    ): MaterialDto {
        return new MaterialDto(
            name: $name,
            description: $description,
            count: $count,
            price: $price
        );
    }
}
