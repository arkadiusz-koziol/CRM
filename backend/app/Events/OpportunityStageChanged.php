<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class OpportunityStageChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $opportunityId,
        public readonly string $oldStageId,
        public readonly string $newStageId,
        public readonly string $oldStageName,
        public readonly string $newStageName,
        public readonly string $changedByUserId,
        public readonly string $changedByName,
        public readonly array $observers = []
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('entity.opportunity.'.$this->opportunityId),
        ];

        // Add private channels for observers
        foreach ($this->observers as $observerId) {
            $channels[] = new PrivateChannel('user.'.$observerId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'opportunity.stage.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'opportunity_id' => $this->opportunityId,
            'old_stage_id' => $this->oldStageId,
            'new_stage_id' => $this->newStageId,
            'old_stage_name' => $this->oldStageName,
            'new_stage_name' => $this->newStageName,
            'changed_by_user_id' => $this->changedByUserId,
            'changed_by_name' => $this->changedByName,
            'timestamp' => now()->toISOString(),
        ];
    }
}
