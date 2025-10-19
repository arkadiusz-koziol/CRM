<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Collab\Entity\Comment;
use Illuminate\Http\Resources\Json\JsonResource;

final class CommentResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Comment $comment */
        $comment = $this->resource;

        return [
            'data' => [
                'type' => 'comments',
                'id' => $comment->id(),
                'attributes' => [
                    'content' => $comment->content(),
                    'content_html' => $comment->contentHtml(),
                    'commentable_type' => $comment->commentableType(),
                    'commentable_id' => $comment->commentableId(),
                    'author_id' => $comment->authorId(),
                    'parent_id' => $comment->parentId(),
                    'is_private' => $comment->isPrivate(),
                    'mentions' => $comment->mentions(),
                    'is_reply' => $comment->isReply(),
                    'created_at' => $comment->createdAt()->toISOString(),
                    'updated_at' => $comment->updatedAt()->toISOString(),
                ],
            ],
        ];
    }
}
