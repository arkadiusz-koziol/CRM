<?php

namespace App\Factory;

use App\Dto\EstateDto;
use App\Models\City;

class EstateDtoFactory
{
    public static function fromRequest(
        string $name,
        string $customId,
        string $street,
        string $postalCode,
        City $city,
        string $houseNumber
    ): EstateDto {
        return new EstateDto(
            name: $name,
            custom_id: $customId,
            street: $street,
            postal_code: $postalCode,
            city: $city,
            house_number: $houseNumber
        );
    }
}
