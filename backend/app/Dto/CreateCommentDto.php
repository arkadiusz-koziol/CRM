<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class CreateCommentDto
{
    public function __construct(
        private string $content,
        private string $commentableType,
        private string $commentableId,
        private ?string $parentId = null,
        private bool $isPrivate = false,
    ) {}

    public function content(): string
    {
        return $this->content;
    }

    public function commentableType(): string
    {
        return $this->commentableType;
    }

    public function commentableId(): string
    {
        return $this->commentableId;
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }
}
