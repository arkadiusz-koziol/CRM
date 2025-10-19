<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Collab\Entity\Mention as MentionEntity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ramsey\Uuid\UuidInterface;

interface MentionRepositoryInterface
{
    public function save(MentionEntity $mention): void;

    public function findByUuid(UuidInterface $id): ?MentionEntity;

    public function findByCommentAndUser(UuidInterface $commentId, int $userId): ?MentionEntity;

    public function findByMentionedUser(int $userId, int $limit = 50): Collection;

    public function countByMentionedUser(int $userId): int;

    public function countUnreadByMentionedUser(int $userId): int;

    public function countByMentionedUserSince(int $userId, Carbon $since): int;

    public function findByEntity(string $entityType, UuidInterface $entityId): Collection;
}
