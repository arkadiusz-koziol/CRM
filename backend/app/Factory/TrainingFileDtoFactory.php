<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\TrainingFileDto;

final class TrainingFileDtoFactory
{
    public function fromArray(array $data): TrainingFileDto
    {
        return new TrainingFileDto(
            originalName: $data['original_name'],
            fileName: $data['file_name'],
            filePath: $data['file_path'],
            mimeType: $data['mime_type'],
            fileSize: $data['file_size'],
        );
    }
}
