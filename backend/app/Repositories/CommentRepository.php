<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Collab\Entity\Comment;
use App\Infrastructure\Collab\CommentMapper;
use App\Interfaces\Repositories\CommentRepositoryInterface;
use App\Models\Comment as CommentModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private CommentMapper $mapper
    ) {}

    public function findById(string $id): ?Comment
    {
        $model = CommentModel::find($id);

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findByCommentable(string $commentableType, string $commentableId, int $perPage = 15): LengthAwarePaginator
    {
        $models = CommentModel::where('commentable_type', $commentableType)
            ->where('commentable_id', $commentableId)
            ->whereNull('parent_id') // Only top-level comments
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $models->getCollection()->transform(fn (CommentModel $model) => $this->mapper->toDomain($model));

        return $models;
    }

    public function findByAuthor(int $authorId, int $perPage = 15): LengthAwarePaginator
    {
        $models = CommentModel::where('author_id', $authorId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $models->getCollection()->transform(fn (CommentModel $model) => $this->mapper->toDomain($model));

        return $models;
    }

    public function findReplies(string $parentId, int $perPage = 15): LengthAwarePaginator
    {
        $models = CommentModel::where('parent_id', $parentId)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        $models->getCollection()->transform(fn (CommentModel $model) => $this->mapper->toDomain($model));

        return $models;
    }

    public function findVisibleForUser(string $commentableType, string $commentableId, int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $models = CommentModel::where('commentable_type', $commentableType)
            ->where('commentable_id', $commentableId)
            ->whereNull('parent_id')
            ->where(function ($query) use ($userId, $commentableType, $commentableId) {
                // Public comments are visible to everyone
                $query->where('is_private', false)
                    // Author can always see their own comments
                    ->orWhere('author_id', $userId)
                    // Team members can see private comments on entities they have access to
                    ->orWhere(function ($subQuery) use ($userId, $commentableType, $commentableId) {
                        $subQuery->where('is_private', true)
                            ->whereHas('commentable', function ($entityQuery) use ($userId, $commentableType) {
                                $this->addEntityAccessConditions($entityQuery, $userId, $commentableType);
                            });
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $models->getCollection()->transform(fn (CommentModel $model) => $this->mapper->toDomain($model));

        return $models;
    }

    public function save(Comment $comment): void
    {
        DB::transaction(function () use ($comment): void {
            $model = $this->mapper->toModel($comment);
            $model->save();
        });
    }

    public function delete(string $id): void
    {
        DB::transaction(function () use ($id): void {
            CommentModel::where('id', $id)->delete();
        });
    }

    public function restore(string $id): void
    {
        DB::transaction(function () use ($id): void {
            CommentModel::withTrashed()->where('id', $id)->restore();
        });
    }

    public function countByCommentable(string $commentableType, string $commentableId): int
    {
        return CommentModel::where('commentable_type', $commentableType)
            ->where('commentable_id', $commentableId)
            ->count();
    }

    public function countByAuthor(int $authorId): int
    {
        return CommentModel::where('author_id', $authorId)->count();
    }

    private function addEntityAccessConditions($query, int $userId, string $commentableType): void
    {
        switch ($commentableType) {
            case 'App\\Models\\Company':
                $query->where(function ($companyQuery) use ($userId) {
                    $companyQuery->where('created_by', $userId)
                        ->orWhereHas('users', function ($userQuery) use ($userId) {
                            $userQuery->where('user_id', $userId);
                        });
                });
                break;
            case 'App\\Models\\Contact':
                $query->where(function ($contactQuery) use ($userId) {
                    $contactQuery->where('owner_user_id', $userId)
                        ->orWhereHas('companies.users', function ($userQuery) use ($userId) {
                            $userQuery->where('user_id', $userId);
                        });
                });
                break;
            case 'App\\Models\\Opportunity':
                $query->where(function ($opportunityQuery) use ($userId) {
                    $opportunityQuery->where('owner_user_id', $userId)
                        ->orWhereHas('company.users', function ($userQuery) use ($userId) {
                            $userQuery->where('user_id', $userId);
                        });
                });
                break;
            case 'App\\Models\\Task':
                $query->where(function ($taskQuery) use ($userId) {
                    $taskQuery->where('assigned_to', $userId)
                        ->orWhere('created_by', $userId);
                });
                break;
            default:
                // For other entity types, only allow access to the creator
                $query->where('created_by', $userId);
                break;
        }
    }
}
