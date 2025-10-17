<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class TrainingFileDto
{
    public function __construct(
        private string $originalName,
        private string $fileName,
        private string $filePath,
        private string $mimeType,
        private int $fileSize,
    ) {}

    public function originalName(): string
    {
        return $this->originalName;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    public function filePath(): string
    {
        return $this->filePath;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function fileSize(): int
    {
        return $this->fileSize;
    }
}
