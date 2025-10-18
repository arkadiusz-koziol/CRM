<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\TrainingCategoryDto;

final class TrainingCategoryDtoFactory
{
    public static function fromArray(array $data): TrainingCategoryDto
    {
        return new TrainingCategoryDto(
            name: $data['name'],
        );
    }
}
