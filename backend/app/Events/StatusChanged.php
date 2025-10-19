<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class StatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $entityType,
        public readonly string $entityId,
        public readonly string $oldStatus,
        public readonly string $newStatus,
        public readonly string $changedByUserId,
        public readonly string $changedByName,
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
        return 'status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'changed_by_user_id' => $this->changedByUserId,
            'changed_by_name' => $this->changedByName,
            'timestamp' => now()->toISOString(),
        ];
    }
}
