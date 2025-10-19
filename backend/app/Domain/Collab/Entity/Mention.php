<?php

declare(strict_types=1);

namespace App\Domain\Collab\Entity;

use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;

final class Mention
{
    public function __construct(
        private readonly UuidInterface $id,
        private readonly UuidInterface $commentId,
        private readonly int $mentionedUserId,
        private readonly int $mentionerUserId,
        private readonly string $entityType,
        private readonly UuidInterface $entityId,
        private readonly Carbon $createdAt,
        private readonly ?Carbon $notifiedAt = null,
        private readonly ?Carbon $readAt = null,
    ) {}

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function commentId(): UuidInterface
    {
        return $this->commentId;
    }

    public function mentionedUserId(): int
    {
        return $this->mentionedUserId;
    }

    public function mentionerUserId(): int
    {
        return $this->mentionerUserId;
    }

    public function entityType(): string
    {
        return $this->entityType;
    }

    public function entityId(): UuidInterface
    {
        return $this->entityId;
    }

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }

    public function notifiedAt(): ?Carbon
    {
        return $this->notifiedAt;
    }

    public function readAt(): ?Carbon
    {
        return $this->readAt;
    }

    public function isNotified(): bool
    {
        return $this->notifiedAt !== null;
    }

    public function isRead(): bool
    {
        return $this->readAt !== null;
    }

    public function markAsNotified(Carbon $notifiedAt): self
    {
        return new self(
            $this->id,
            $this->commentId,
            $this->mentionedUserId,
            $this->mentionerUserId,
            $this->entityType,
            $this->entityId,
            $this->createdAt,
            $notifiedAt,
            $this->readAt,
        );
    }

    public function markAsRead(Carbon $readAt): self
    {
        return new self(
            $this->id,
            $this->commentId,
            $this->mentionedUserId,
            $this->mentionerUserId,
            $this->entityType,
            $this->entityId,
            $this->createdAt,
            $this->notifiedAt,
            $readAt,
        );
    }

    public static function create(
        UuidInterface $id,
        UuidInterface $commentId,
        int $mentionedUserId,
        int $mentionerUserId,
        string $entityType,
        UuidInterface $entityId,
        Carbon $createdAt,
    ): self {
        return new self(
            $id,
            $commentId,
            $mentionedUserId,
            $mentionerUserId,
            $entityType,
            $entityId,
            $createdAt,
        );
    }
}
