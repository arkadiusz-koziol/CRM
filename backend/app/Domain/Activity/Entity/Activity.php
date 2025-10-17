<?php

declare(strict_types=1);

namespace App\Domain\Activity\Entity;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

final readonly class Activity
{
    public function __construct(
        private string $id,
        private string $action,
        private string $userName,
        private string $userEmail,
        private string $entityType,
        private ?string $entityId,
        private Carbon $createdAt
    ) {}

    public static function create(
        string $action,
        string $userName,
        string $userEmail,
        string $entityType,
        ?string $entityId = null
    ): self {
        return new self(
            id: Uuid::uuid4()->toString(),
            action: $action,
            userName: $userName,
            userEmail: $userEmail,
            entityType: $entityType,
            entityId: $entityId,
            createdAt: Carbon::now()
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function userName(): string
    {
        return $this->userName;
    }

    public function userEmail(): string
    {
        return $this->userEmail;
    }

    public function entityType(): string
    {
        return $this->entityType;
    }

    public function entityId(): ?string
    {
        return $this->entityId;
    }

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }
}
