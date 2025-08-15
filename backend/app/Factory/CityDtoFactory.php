<?php

namespace App\Factory;

use App\Dto\CityDto;

class CityDtoFactory
{
    public static function fromRequest(
        string $name,
        string $district,
        string $commune,
        string $voivodeship
    ): CityDto {
        return new CityDto(
            name: $name,
            district: $district,
            commune: $commune,
            voivodeship: $voivodeship
        );
    }
}
