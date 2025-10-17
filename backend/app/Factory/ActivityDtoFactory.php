<?php

declare(strict_types=1);

namespace App\Factory;

use App\Domain\Activity\Entity\Activity;
use App\Dto\ActivityDto;
use Carbon\Carbon;

final class ActivityDtoFactory
{
    public static function fromEntity(Activity $activity): ActivityDto
    {
        return new ActivityDto(
            id: $activity->id(),
            action: $activity->action(),
            userName: $activity->userName(),
            userEmail: $activity->userEmail(),
            entityType: $activity->entityType(),
            entityId: $activity->entityId(),
            createdAt: $activity->createdAt()
        );
    }

    public static function fromArray(array $data): ActivityDto
    {
        return new ActivityDto(
            id: $data['id'],
            action: $data['action'],
            userName: $data['user_name'],
            userEmail: $data['user_email'],
            entityType: $data['entity_type'],
            entityId: $data['entity_id'] ?? null,
            createdAt: Carbon::parse($data['created_at'])
        );
    }
}
