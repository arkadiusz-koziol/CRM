<?php

declare(strict_types=1);

namespace App\Factory\Export;

use App\Dto\Export\CreateExportDto;
use Carbon\Carbon;

final class CreateExportDtoFactory
{
    public function __construct(
        private Carbon $carbon,
    ) {}

    public function fromArray(array $data): CreateExportDto
    {
        return new CreateExportDto(
            filename: $data['filename'] ?? 'export_'.$this->carbon->format('Y-m-d_H-i-s'),
            format: $data['format'],
            data: $data['data'] ?? [],
            headers: $data['headers'] ?? [],
            options: $data['options'] ?? [],
        );
    }
}
