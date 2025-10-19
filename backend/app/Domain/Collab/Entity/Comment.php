<?php

declare(strict_types=1);

namespace App\Domain\Collab\Entity;

use Carbon\Carbon;

final class Comment
{
    public function __construct(
        private string $id,
        private string $content,
        private ?string $contentHtml,
        private string $commentableType,
        private string $commentableId,
        private int $authorId,
        private ?string $parentId,
        private bool $isPrivate,
        private ?array $mentions,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function contentHtml(): ?string
    {
        return $this->contentHtml;
    }

    public function commentableType(): string
    {
        return $this->commentableType;
    }

    public function commentableId(): string
    {
        return $this->commentableId;
    }

    public function authorId(): int
    {
        return $this->authorId;
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function mentions(): ?array
    {
        return $this->mentions;
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

    public static function create(
        string $id,
        string $content,
        string $commentableType,
        string $commentableId,
        int $authorId,
        ?string $parentId = null,
        bool $isPrivate = false,
        ?array $mentions = null,
    ): self {
        $now = Carbon::now();

        return new self(
            id: $id,
            content: $content,
            contentHtml: null, // Will be set by service
            commentableType: $commentableType,
            commentableId: $commentableId,
            authorId: $authorId,
            parentId: $parentId,
            isPrivate: $isPrivate,
            mentions: $mentions,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function updateContent(string $content, ?string $contentHtml = null): void
    {
        $this->content = $content;
        $this->contentHtml = $contentHtml;
        $this->updatedAt = Carbon::now();
    }

    public function updateVisibility(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
        $this->updatedAt = Carbon::now();
    }

    public function updateMentions(?array $mentions): void
    {
        $this->mentions = $mentions;
        $this->updatedAt = Carbon::now();
    }

    public function markAsDeleted(): void
    {
        $this->deletedAt = Carbon::now();
        $this->updatedAt = Carbon::now();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
        $this->updatedAt = Carbon::now();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isReply(): bool
    {
        return $this->parentId !== null;
    }
}
