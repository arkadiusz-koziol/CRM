<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CreateCommentDto;

final class CreateCommentDtoFactory
{
    public function fromArray(array $data): CreateCommentDto
    {
        return new CreateCommentDto(
            content: $data['content'],
            commentableType: $data['commentable_type'],
            commentableId: $data['commentable_id'],
            parentId: $data['parent_id'] ?? null,
            isPrivate: $data['is_private'] ?? false,
        );
    }
}
