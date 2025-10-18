<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\CommentAdded;
use Illuminate\Broadcasting\PrivateChannel;
use Tests\TestCase;

final class CommentAddedTest extends TestCase
{
    public function test_it_implements_should_broadcast(): void
    {
        $event = new CommentAdded(
            'comment-123',
            'company',
            'company-456',
            'user-789',
            'John Doe',
            'This is a comment',
            ['user-111', 'user-222']
        );

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }

    public function test_it_broadcasts_on_entity_and_observer_channels(): void
    {
        $event = new CommentAdded(
            'comment-123',
            'company',
            'company-456',
            'user-789',
            'John Doe',
            'This is a comment',
            ['user-111', 'user-222']
        );

        $channels = $event->broadcastOn();

        $this->assertCount(3, $channels);
        
        // Check entity channel
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals('entity.company.company-456', $channels[0]->name);
        
        // Check observer channels
        $this->assertInstanceOf(PrivateChannel::class, $channels[1]);
        $this->assertEquals('user.user-111', $channels[1]->name);
        
        $this->assertInstanceOf(PrivateChannel::class, $channels[2]);
        $this->assertEquals('user.user-222', $channels[2]->name);
    }

    public function test_it_broadcasts_with_correct_event_name(): void
    {
        $event = new CommentAdded(
            'comment-123',
            'company',
            'company-456',
            'user-789',
            'John Doe',
            'This is a comment',
            []
        );

        $this->assertEquals('comment.added', $event->broadcastAs());
    }

    public function test_it_broadcasts_with_correct_payload(): void
    {
        $event = new CommentAdded(
            'comment-123',
            'company',
            'company-456',
            'user-789',
            'John Doe',
            'This is a comment',
            ['user-111', 'user-222']
        );

        $payload = $event->broadcastWith();

        $this->assertArrayHasKey('comment_id', $payload);
        $this->assertArrayHasKey('entity_type', $payload);
        $this->assertArrayHasKey('entity_id', $payload);
        $this->assertArrayHasKey('author_id', $payload);
        $this->assertArrayHasKey('author_name', $payload);
        $this->assertArrayHasKey('comment', $payload);
        $this->assertArrayHasKey('timestamp', $payload);

        $this->assertEquals('comment-123', $payload['comment_id']);
        $this->assertEquals('company', $payload['entity_type']);
        $this->assertEquals('company-456', $payload['entity_id']);
        $this->assertEquals('user-789', $payload['author_id']);
        $this->assertEquals('John Doe', $payload['author_name']);
        $this->assertEquals('This is a comment', $payload['comment']);
        $this->assertIsString($payload['timestamp']);
    }

    public function test_it_handles_empty_observers(): void
    {
        $event = new CommentAdded(
            'comment-123',
            'company',
            'company-456',
            'user-789',
            'John Doe',
            'This is a comment',
            []
        );

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals('entity.company.company-456', $channels[0]->name);
    }
}
