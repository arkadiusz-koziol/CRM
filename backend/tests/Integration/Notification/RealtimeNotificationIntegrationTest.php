<?php

declare(strict_types=1);

namespace Tests\Integration\Notification;

use App\Events\CommentAdded;
use App\Events\OpportunityStageChanged;
use App\Events\StatusChanged;
use App\Events\TaskAssigned;
use App\Models\User;
use App\Services\Notification\RealtimeNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class RealtimeNotificationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private RealtimeNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = app(RealtimeNotificationService::class);
        
        Event::fake();
    }

    public function test_it_handles_end_to_end_task_assignment(): void
    {
        $assignedByUser = User::factory()->create();
        $assignedToUser = User::factory()->create();

        $this->service->notifyTaskAssigned(
            'task-123',
            (string) $assignedToUser->id,
            (string) $assignedByUser->id,
            'Test Task',
            'Test Description',
            '2024-12-31'
        );

        Event::assertDispatched(TaskAssigned::class, function ($event) use ($assignedToUser, $assignedByUser) {
            return $event->taskId === 'task-123' &&
                   $event->assignedToUserId === (string) $assignedToUser->id &&
                   $event->assignedByUserId === (string) $assignedByUser->id &&
                   $event->taskTitle === 'Test Task' &&
                   $event->taskDescription === 'Test Description' &&
                   $event->dueDate === '2024-12-31';
        });
    }

    public function test_it_handles_end_to_end_comment_addition(): void
    {
        $author = User::factory()->create();
        $observer1 = User::factory()->create();
        $observer2 = User::factory()->create();

        $this->service->notifyCommentAdded(
            'comment-123',
            'company',
            'company-456',
            (string) $author->id,
            $author->name,
            'This is a comment',
            [(string) $observer1->id, (string) $observer2->id]
        );

        Event::assertDispatched(CommentAdded::class, function ($event) use ($author, $observer1, $observer2) {
            return $event->commentId === 'comment-123' &&
                   $event->entityType === 'company' &&
                   $event->entityId === 'company-456' &&
                   $event->authorId === (string) $author->id &&
                   $event->authorName === $author->name &&
                   $event->comment === 'This is a comment' &&
                   $event->observers === [(string) $observer1->id, (string) $observer2->id];
        });
    }

    public function test_it_handles_end_to_end_status_change(): void
    {
        $changedByUser = User::factory()->create();
        $observer1 = User::factory()->create();
        $observer2 = User::factory()->create();

        $this->service->notifyStatusChanged(
            'company',
            'company-456',
            'active',
            'inactive',
            (string) $changedByUser->id,
            $changedByUser->name,
            [(string) $observer1->id, (string) $observer2->id]
        );

        Event::assertDispatched(StatusChanged::class, function ($event) use ($changedByUser, $observer1, $observer2) {
            return $event->entityType === 'company' &&
                   $event->entityId === 'company-456' &&
                   $event->oldStatus === 'active' &&
                   $event->newStatus === 'inactive' &&
                   $event->changedByUserId === (string) $changedByUser->id &&
                   $event->changedByName === $changedByUser->name &&
                   $event->observers === [(string) $observer1->id, (string) $observer2->id];
        });
    }

    public function test_it_handles_end_to_end_opportunity_stage_change(): void
    {
        $changedByUser = User::factory()->create();
        $observer1 = User::factory()->create();
        $observer2 = User::factory()->create();

        $this->service->notifyOpportunityStageChanged(
            'opportunity-123',
            'stage-456',
            'stage-789',
            'Prospecting',
            'Demo',
            (string) $changedByUser->id,
            $changedByUser->name,
            [(string) $observer1->id, (string) $observer2->id]
        );

        Event::assertDispatched(OpportunityStageChanged::class, function ($event) use ($changedByUser, $observer1, $observer2) {
            return $event->opportunityId === 'opportunity-123' &&
                   $event->oldStageId === 'stage-456' &&
                   $event->newStageId === 'stage-789' &&
                   $event->oldStageName === 'Prospecting' &&
                   $event->newStageName === 'Demo' &&
                   $event->changedByUserId === (string) $changedByUser->id &&
                   $event->changedByName === $changedByUser->name &&
                   $event->observers === [(string) $observer1->id, (string) $observer2->id];
        });
    }

    public function test_it_handles_multiple_observers(): void
    {
        $author = User::factory()->create();
        $observers = User::factory()->count(5)->create();

        $this->service->notifyCommentAdded(
            'comment-123',
            'company',
            'company-456',
            (string) $author->id,
            $author->name,
            'This is a comment',
            $observers->pluck('id')->map(fn($id) => (string) $id)->toArray()
        );

        Event::assertDispatched(CommentAdded::class, function ($event) use ($observers) {
            return $event->observers === $observers->pluck('id')->map(fn($id) => (string) $id)->toArray();
        });
    }

    public function test_it_handles_empty_observers(): void
    {
        $author = User::factory()->create();

        $this->service->notifyCommentAdded(
            'comment-123',
            'company',
            'company-456',
            (string) $author->id,
            $author->name,
            'This is a comment',
            []
        );

        Event::assertDispatched(CommentAdded::class, function ($event) {
            return $event->observers === [];
        });
    }
}
