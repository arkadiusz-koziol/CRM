<?php

declare(strict_types=1);

namespace App\Interfaces\Services;

use Ramsey\Uuid\UuidInterface;

interface RealtimeNotificationServiceInterface
{
    public function sendMentionNotification(
        UuidInterface $mentionedUserId,
        UuidInterface $mentionerUserId,
        string $entityType,
        UuidInterface $entityId,
        UuidInterface $commentId
    ): void;

    public function sendMentionEmail(
        UuidInterface $mentionedUserId,
        UuidInterface $mentionerUserId,
        string $entityType,
        UuidInterface $entityId,
        UuidInterface $commentId
    ): void;
}
