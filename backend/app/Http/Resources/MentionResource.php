<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Collab\Entity\Mention as MentionEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MentionResource extends JsonResource
{
    public function __construct(private readonly MentionEntity $mention)
    {
        parent::__construct($mention);
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'type' => 'mentions',
                'id' => $this->mention->id()->toString(),
                'attributes' => [
                    'comment_id' => $this->mention->commentId()->toString(),
                    'mentioned_user_id' => $this->mention->mentionedUserId(),
                    'mentioner_user_id' => $this->mention->mentionerUserId(),
                    'entity_type' => $this->mention->entityType(),
                    'entity_id' => $this->mention->entityId()->toString(),
                    'created_at' => $this->mention->createdAt()->toISOString(),
                    'notified_at' => $this->mention->notifiedAt()?->toISOString(),
                    'read_at' => $this->mention->readAt()?->toISOString(),
                    'is_notified' => $this->mention->isNotified(),
                    'is_read' => $this->mention->isRead(),
                ],
            ],
            'meta' => [
                'request_id' => app('requestId'),
            ],
        ];
    }
}
