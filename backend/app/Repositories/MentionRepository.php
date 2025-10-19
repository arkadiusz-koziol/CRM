<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Collab\Entity\Mention as MentionEntity;
use App\Infrastructure\Collab\MentionMapper;
use App\Interfaces\Repositories\MentionRepositoryInterface;
use App\Models\Mention as MentionModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ramsey\Uuid\UuidInterface;

final class MentionRepository extends EloquentRepository implements MentionRepositoryInterface
{
    public function __construct(MentionModel $model)
    {
        parent::__construct($model);
    }

    public function save(MentionEntity $mention): void
    {
        $this->transactional(function () use ($mention): void {
            $model = MentionMapper::toModel($mention);
            $model->saveOrFail();
        });
    }

    public function findByUuid(UuidInterface $id): ?MentionEntity
    {
        $model = $this->query()->where('id', $id->toString())->first();

        return $model ? MentionMapper::toDomain($model) : null;
    }

    public function findByCommentAndUser(UuidInterface $commentId, int $userId): ?MentionEntity
    {
        $model = $this->query()
            ->where('comment_id', $commentId->toString())
            ->where('mentioned_user_id', $userId)
            ->first();

        return $model ? MentionMapper::toDomain($model) : null;
    }

    public function findByMentionedUser(int $userId, int $limit = 50): Collection
    {
        return $this->query()
            ->where('mentioned_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn (MentionModel $model) => MentionMapper::toDomain($model));
    }

    public function countByMentionedUser(int $userId): int
    {
        return $this->query()
            ->where('mentioned_user_id', $userId)
            ->count();
    }

    public function countUnreadByMentionedUser(int $userId): int
    {
        return $this->query()
            ->where('mentioned_user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function countByMentionedUserSince(int $userId, Carbon $since): int
    {
        return $this->query()
            ->where('mentioned_user_id', $userId)
            ->where('created_at', '>=', $since)
            ->count();
    }

    public function findByEntity(string $entityType, UuidInterface $entityId): Collection
    {
        return $this->query()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId->toString())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (MentionModel $model) => MentionMapper::toDomain($model));
    }
}
