<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

final readonly class TrainingDto implements Arrayable
{
    public function __construct(
        private string $title,
        private ?string $description,
        private string $category,
        private ?string $categoryId,
        private ?string $filePath,
        private ?string $fileName,
        private ?int $fileSize,
        private ?string $mimeType,
    ) {}

    public function title(): string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function categoryId(): ?string
    {
        return $this->categoryId;
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'category_id' => $this->categoryId,
            'file_path' => $this->filePath,
            'file_name' => $this->fileName,
            'file_size' => $this->fileSize,
            'mime_type' => $this->mimeType,
        ];
    }
}
