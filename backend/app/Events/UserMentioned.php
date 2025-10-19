<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class UserMentioned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $mentionedUserId,
        public readonly string $mentionerUserId,
        public readonly string $entityType,
        public readonly string $entityId,
        public readonly string $commentId,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->mentionedUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.mentioned';
    }

    public function broadcastWith(): array
    {
        return [
            'mentioned_user_id' => $this->mentionedUserId,
            'mentioner_user_id' => $this->mentionerUserId,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'comment_id' => $this->commentId,
            'timestamp' => now()->toISOString(),
        ];
    }
}
