<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $taskId,
        public readonly string $assignedToUserId,
        public readonly string $assignedByUserId,
        public readonly string $taskTitle,
        public readonly string $taskDescription,
        public readonly ?string $dueDate = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->assignedToUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'task_id' => $this->taskId,
            'assigned_to_user_id' => $this->assignedToUserId,
            'assigned_by_user_id' => $this->assignedByUserId,
            'task_title' => $this->taskTitle,
            'task_description' => $this->taskDescription,
            'due_date' => $this->dueDate,
            'timestamp' => now()->toISOString(),
        ];
    }
}
