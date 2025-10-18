<?php

declare(strict_types=1);

namespace App\Infrastructure\Contact;

use App\Domain\Crm\Entity\Contact as ContactEntity;
use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use App\Models\Contact as ContactModel;
use Carbon\Carbon;

final readonly class ContactMapper
{
    public function toDomain(ContactModel $model): ContactEntity
    {
        return new ContactEntity(
            id: $model->id,
            firstName: $model->first_name,
            lastName: $model->last_name,
            email: $model->email,
            phone: $model->phone,
            leadLevel: $model->lead_level instanceof LeadLevel ? $model->lead_level : LeadLevel::from($model->lead_level),
            ownerUserId: $model->owner_user_id ? (string) $model->owner_user_id : null,
            source: $model->source,
            status: $model->status instanceof ContactStatus ? $model->status : ContactStatus::from($model->status),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }

    public function toModel(ContactEntity $entity): ContactModel
    {
        $model = new ContactModel;
        $model->id = $entity->id();
        $model->first_name = $entity->firstName();
        $model->last_name = $entity->lastName();
        $model->email = $entity->email();
        $model->phone = $entity->phone();
        $model->lead_level = $entity->leadLevel();
        $model->owner_user_id = $entity->ownerUserId();
        $model->source = $entity->source();
        $model->status = $entity->status();
        $model->created_at = $entity->createdAt();
        $model->updated_at = $entity->updatedAt();
        $model->deleted_at = $entity->deletedAt();

        return $model;
    }
}
