<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\TaskAssigned;
use Illuminate\Broadcasting\PrivateChannel;
use Tests\TestCase;

final class TaskAssignedTest extends TestCase
{
    public function test_it_implements_should_broadcast(): void
    {
        $event = new TaskAssigned(
            'task-123',
            'user-456',
            'user-789',
            'Test Task',
            'Test Description',
            '2024-12-31'
        );

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }

    public function test_it_broadcasts_on_user_channel(): void
    {
        $event = new TaskAssigned(
            'task-123',
            'user-456',
            'user-789',
            'Test Task',
            'Test Description',
            '2024-12-31'
        );

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals('user.user-456', $channels[0]->name);
    }

    public function test_it_broadcasts_with_correct_event_name(): void
    {
        $event = new TaskAssigned(
            'task-123',
            'user-456',
            'user-789',
            'Test Task',
            'Test Description',
            '2024-12-31'
        );

        $this->assertEquals('task.assigned', $event->broadcastAs());
    }

    public function test_it_broadcasts_with_correct_payload(): void
    {
        $event = new TaskAssigned(
            'task-123',
            'user-456',
            'user-789',
            'Test Task',
            'Test Description',
            '2024-12-31'
        );

        $payload = $event->broadcastWith();

        $this->assertArrayHasKey('task_id', $payload);
        $this->assertArrayHasKey('assigned_to_user_id', $payload);
        $this->assertArrayHasKey('assigned_by_user_id', $payload);
        $this->assertArrayHasKey('task_title', $payload);
        $this->assertArrayHasKey('task_description', $payload);
        $this->assertArrayHasKey('due_date', $payload);
        $this->assertArrayHasKey('timestamp', $payload);

        $this->assertEquals('task-123', $payload['task_id']);
        $this->assertEquals('user-456', $payload['assigned_to_user_id']);
        $this->assertEquals('user-789', $payload['assigned_by_user_id']);
        $this->assertEquals('Test Task', $payload['task_title']);
        $this->assertEquals('Test Description', $payload['task_description']);
        $this->assertEquals('2024-12-31', $payload['due_date']);
        $this->assertIsString($payload['timestamp']);
    }

    public function test_it_handles_null_due_date(): void
    {
        $event = new TaskAssigned(
            'task-123',
            'user-456',
            'user-789',
            'Test Task',
            'Test Description',
            null
        );

        $payload = $event->broadcastWith();

        $this->assertNull($payload['due_date']);
    }
}
