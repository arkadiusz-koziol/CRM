<?php

declare(strict_types=1);

namespace App\Config;

final class ExportConfig
{
    public function __construct(
        private string $storagePath,
        private int $maxFileSize,
        private int $chunkSize,
        private int $fileTtl,
        private string $logoPath,
        private string $footerText,
    ) {}

    public function storagePath(): string
    {
        return $this->storagePath;
    }

    public function maxFileSize(): int
    {
        return $this->maxFileSize;
    }

    public function chunkSize(): int
    {
        return $this->chunkSize;
    }

    public function fileTtl(): int
    {
        return $this->fileTtl;
    }

    public function logoPath(): string
    {
        return $this->logoPath;
    }

    public function footerText(): string
    {
        return $this->footerText;
    }
}
