<?php

declare(strict_types=1);

namespace App\Dto;

readonly class CreateCompanyDto
{
    public function __construct(
        private string $name,
        private ?string $industry,
        private string $source,
        private string $status,
        private ?string $region,
        private ?string $vatId
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function industry(): ?string
    {
        return $this->industry;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function region(): ?string
    {
        return $this->region;
    }

    public function vatId(): ?string
    {
        return $this->vatId;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'industry' => $this->industry,
            'source' => $this->source,
            'status' => $this->status,
            'region' => $this->region,
            'vat_id' => $this->vatId,
        ];
    }
}
