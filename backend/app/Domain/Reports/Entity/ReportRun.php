<?php

declare(strict_types=1);

namespace App\Domain\Reports\Entity;

use Carbon\Carbon;

final class ReportRun
{
    public function __construct(
        private string $id,
        private string $reportId,
        private string $runBy,
        private string $status,
        private ?array $parameters,
        private ?string $filePath,
        private ?string $fileName,
        private ?int $fileSize,
        private ?string $mimeType,
        private ?Carbon $startedAt,
        private ?Carbon $completedAt,
        private ?string $errorMessage,
        private Carbon $createdAt,
        private Carbon $updatedAt,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function reportId(): string
    {
        return $this->reportId;
    }

    public function runBy(): string
    {
        return $this->runBy;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function parameters(): ?array
    {
        return $this->parameters;
    }

    public function filePath(): ?string
    {
        return $this->filePath;
    }

    public function fileName(): ?string
    {
        return $this->fileName;
    }

    public function fileSize(): ?int
    {
        return $this->fileSize;
    }

    public function mimeType(): ?string
    {
        return $this->mimeType;
    }

    public function startedAt(): ?Carbon
    {
        return $this->startedAt;
    }

    public function completedAt(): ?Carbon
    {
        return $this->completedAt;
    }

    public function errorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }

    public function updatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function start(): void
    {
        $this->status = 'running';
        $this->startedAt = Carbon::now();
    }

    public function complete(string $filePath, string $fileName, int $fileSize, string $mimeType): void
    {
        $this->status = 'completed';
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->fileSize = $fileSize;
        $this->mimeType = $mimeType;
        $this->completedAt = Carbon::now();
    }

    public function fail(string $errorMessage): void
    {
        $this->status = 'failed';
        $this->errorMessage = $errorMessage;
        $this->completedAt = Carbon::now();
    }

    public static function create(
        string $id,
        string $reportId,
        string $runBy,
        ?array $parameters = null,
    ): self {
        $now = Carbon::now();

        return new self(
            $id,
            $reportId,
            $runBy,
            'pending',
            $parameters,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $now,
            $now,
        );
    }

    public static function reconstitute(
        string $id,
        string $reportId,
        string $runBy,
        string $status,
        ?array $parameters,
        ?string $filePath,
        ?string $fileName,
        ?int $fileSize,
        ?string $mimeType,
        ?Carbon $startedAt,
        ?Carbon $completedAt,
        ?string $errorMessage,
        Carbon $createdAt,
        Carbon $updatedAt,
    ): self {
        return new self(
            $id,
            $reportId,
            $runBy,
            $status,
            $parameters,
            $filePath,
            $fileName,
            $fileSize,
            $mimeType,
            $startedAt,
            $completedAt,
            $errorMessage,
            $createdAt,
            $updatedAt,
        );
    }
}
