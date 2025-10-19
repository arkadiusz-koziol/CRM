<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Entity\User as UserEntity;
use App\Models\User as UserModel;
use Ramsey\Uuid\Uuid;

final class UserMapper
{
    public static function toDomain(UserModel $model): UserEntity
    {
        return new UserEntity(
            Uuid::fromInteger((string) $model->id),
            $model->id,
            $model->name,
            $model->surname,
            $model->email,
            $model->username,
            $model->mention_notifications_enabled ?? true,
            $model->mention_email_notifications_enabled ?? true,
            $model->created_at,
            $model->updated_at,
        );
    }

    public static function toModel(UserEntity $entity): UserModel
    {
        $model = new UserModel;
        $model->id = $entity->intId();
        $model->name = $entity->firstName();
        $model->surname = $entity->lastName();
        $model->email = $entity->email();
        $model->username = $entity->username();
        $model->mention_notifications_enabled = $entity->mentionNotificationsEnabled();
        $model->mention_email_notifications_enabled = $entity->mentionEmailNotificationsEnabled();
        $model->created_at = $entity->createdAt();
        $model->updated_at = $entity->updatedAt();

        return $model;
    }
}
