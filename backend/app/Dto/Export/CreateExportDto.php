<?php

declare(strict_types=1);

namespace App\Dto\Export;

final readonly class CreateExportDto
{
    public function __construct(
        private string $filename,
        private string $format,
        private array $data,
        private array $headers,
        private array $options,
    ) {}

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
}
