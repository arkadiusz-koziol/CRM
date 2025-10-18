<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\TrainingDto;

final class TrainingDtoFactory
{
    /**
     * @param array<string, mixed> $data
     */
    public function fromArray(array $data): TrainingDto
    {
        return new TrainingDto(
            title: $data['title'] ?? '',
            description: $data['description'] ?? null,
            category: $data['category'] ?? '',
            filePath: $data['file_path'] ?? null,
            fileName: $data['file_name'] ?? null,
            fileSize: $data['file_size'] ?? null,
            mimeType: $data['mime_type'] ?? null,
        );
    }
}
