<?php

declare(strict_types=1);

namespace App\Infrastructure\Collab;

use App\Domain\Collab\Entity\Comment;
use App\Models\Comment as CommentModel;

final class CommentMapper
{
    public function toDomain(CommentModel $model): Comment
    {
        return new Comment(
            id: $model->id,
            content: $model->content,
            contentHtml: $model->content_html,
            commentableType: $model->commentable_type,
            commentableId: $model->commentable_id,
            authorId: $model->author_id,
            parentId: $model->parent_id,
            isPrivate: $model->is_private,
            mentions: $model->mentions,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }

    public function toModel(Comment $comment): CommentModel
    {
        return new CommentModel([
            'id' => $comment->id(),
            'content' => $comment->content(),
            'content_html' => $comment->contentHtml(),
            'commentable_type' => $comment->commentableType(),
            'commentable_id' => $comment->commentableId(),
            'author_id' => $comment->authorId(),
            'parent_id' => $comment->parentId(),
            'is_private' => $comment->isPrivate(),
            'mentions' => $comment->mentions(),
            'created_at' => $comment->createdAt(),
            'updated_at' => $comment->updatedAt(),
            'deleted_at' => $comment->deletedAt(),
        ]);
    }
}
