<?php

declare(strict_types=1);

namespace App\Infrastructure\Collab;

use App\Domain\Collab\Entity\Mention as MentionEntity;
use App\Models\Mention as MentionModel;
use Ramsey\Uuid\Uuid;

final class MentionMapper
{
    public static function toDomain(MentionModel $model): MentionEntity
    {
        return new MentionEntity(
            Uuid::fromString($model->id),
            Uuid::fromString($model->comment_id),
            (int) $model->mentioned_user_id,
            (int) $model->mentioner_user_id,
            $model->entity_type,
            Uuid::fromString($model->entity_id),
            $model->created_at,
            $model->notified_at,
            $model->read_at,
        );
    }

    public static function toModel(MentionEntity $entity): MentionModel
    {
        $model = new MentionModel;
        $model->id = $entity->id()->toString();
        $model->comment_id = $entity->commentId()->toString();
        $model->mentioned_user_id = $entity->mentionedUserId();
        $model->mentioner_user_id = $entity->mentionerUserId();
        $model->entity_type = $entity->entityType();
        $model->entity_id = $entity->entityId()->toString();
        $model->created_at = $entity->createdAt();
        $model->notified_at = $entity->notifiedAt();
        $model->read_at = $entity->readAt();

        return $model;
    }
}
