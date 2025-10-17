<?php

declare(strict_types=1);

namespace App\Domain\TrainingFile\Entity;

use Carbon\Carbon;

final class TrainingFile
{
    public function __construct(
        private string $id,
        private string $trainingId,
        private string $originalName,
        private string $fileName,
        private string $filePath,
        private string $mimeType,
        private int $fileSize,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function trainingId(): string
    {
        return $this->trainingId;
    }

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

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }

    public function updatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?Carbon
    {
        return $this->deletedAt;
    }

    public function markAsDeleted(): void
    {
        $this->deletedAt = Carbon::now();
    }

    public static function create(
        string $id,
        string $trainingId,
        string $originalName,
        string $fileName,
        string $filePath,
        string $mimeType,
        int $fileSize,
    ): self {
        $now = Carbon::now();

        return new self(
            id: $id,
            trainingId: $trainingId,
            originalName: $originalName,
            fileName: $fileName,
            filePath: $filePath,
            mimeType: $mimeType,
            fileSize: $fileSize,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
