<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class CommentAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $commentId,
        public readonly string $entityType,
        public readonly string $entityId,
        public readonly string $authorId,
        public readonly string $authorName,
        public readonly string $comment,
        public readonly array $observers = []
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('entity.'.$this->entityType.'.'.$this->entityId),
        ];

        // Add private channels for observers
        foreach ($this->observers as $observerId) {
            $channels[] = new PrivateChannel('user.'.$observerId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'comment.added';
    }

    public function broadcastWith(): array
    {
        return [
            'comment_id' => $this->commentId,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'author_id' => $this->authorId,
            'author_name' => $this->authorName,
            'comment' => $this->comment,
            'timestamp' => now()->toISOString(),
        ];
    }
}
