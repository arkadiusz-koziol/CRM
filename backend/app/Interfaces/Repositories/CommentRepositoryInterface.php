<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Collab\Entity\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CommentRepositoryInterface
{
    public function findById(string $id): ?Comment;

    public function findByCommentable(string $commentableType, string $commentableId, int $perPage = 15): LengthAwarePaginator;

    public function findByAuthor(int $authorId, int $perPage = 15): LengthAwarePaginator;

    public function findReplies(string $parentId, int $perPage = 15): LengthAwarePaginator;

    public function findVisibleForUser(string $commentableType, string $commentableId, int $userId, int $perPage = 15): LengthAwarePaginator;

    public function save(Comment $comment): void;

    public function delete(string $id): void;

    public function restore(string $id): void;

    public function countByCommentable(string $commentableType, string $commentableId): int;

    public function countByAuthor(int $authorId): int;
}
