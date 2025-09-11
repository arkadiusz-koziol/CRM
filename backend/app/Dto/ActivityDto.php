<?php

declare(strict_types=1);

namespace App\Dto;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

final readonly class ActivityDto implements Arrayable
{
    public function __construct(
        private string $id,
        private string $action,
        private string $userName,
        private string $userEmail,
        private string $entityType,
        private ?string $entityId,
        private Carbon $createdAt
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'action' => $this->getAction(),
            'user_name' => $this->getUserName(),
            'user_email' => $this->getUserEmail(),
            'entity_type' => $this->getEntityType(),
            'entity_id' => $this->getEntityId(),
            'created_at' => $this->getCreatedAt()->toISOString(),
            'time_ago' => $this->getTimeAgo(),
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getTimeAgo(): string
    {
        $now = Carbon::now();
        $diff = $this->createdAt->diffInMinutes($now);

        if ($diff < 1) {
            return 'przed chwilą';
        }

        if ($diff < 60) {
            return $diff . ' min temu';
        }

        $hours = $this->createdAt->diffInHours($now);
        if ($hours < 24) {
            return $hours . ' godz. temu';
        }

        $days = $this->createdAt->diffInDays($now);
        if ($days < 7) {
            return $days . ' dni temu';
        }

        return $this->createdAt->format('d.m.Y H:i');
    }
}
