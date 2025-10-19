<?php

declare(strict_types=1);

namespace App\Domain\Export\Entity;

use Carbon\Carbon;

final class Export
{
    public function __construct(
        private string $id,
        private string $filename,
        private string $format,
        private array $data,
        private array $headers,
        private array $options,
        private Carbon $createdAt,
        private ?Carbon $expiresAt = null,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function format(): string
    {
        return $this->format;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function options(): array
    {
        return $this->options;
    }

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }

    public function expiresAt(): ?Carbon
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt !== null && $this->expiresAt->isPast();
    }

    public function getFullFilename(): string
    {
        return $this->filename.'.'.$this->format;
    }
}
