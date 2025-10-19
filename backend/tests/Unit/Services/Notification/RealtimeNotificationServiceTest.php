<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Notification;

use App\Events\CommentAdded;
use App\Events\OpportunityStageChanged;
use App\Events\StatusChanged;
use App\Events\TaskAssigned;
use App\Services\Notification\RealtimeNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

final class RealtimeNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private RealtimeNotificationService $service;

    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->service = new RealtimeNotificationService($this->logger);

        Event::fake();
    }

    public function test_it_dispatches_task_assigned_event(): void
    {
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Task assignment notification sent', $this->isType('array'));

        $this->service->notifyTaskAssigned(
            'task-123',
            'user-456',
            'user-789',
            'Test Task',
            'Test Description',
            '2024-12-31'
        );

        Event::assertDispatched(TaskAssigned::class, function ($event) {
            return $event->taskId === 'task-123' &&
                   $event->assignedToUserId === 'user-456' &&
                   $event->assignedByUserId === 'user-789' &&
                   $event->taskTitle === 'Test Task' &&
                   $event->taskDescription === 'Test Description' &&
                   $event->dueDate === '2024-12-31';
        });
    }

    public function test_it_dispatches_comment_added_event(): void
    {
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Comment notification sent', $this->isType('array'));

        $this->service->notifyCommentAdded(
            'comment-123',
            'company',
            'company-456',
            'user-789',
            'John Doe',
            'This is a comment',
            ['user-111', 'user-222']
        );

        Event::assertDispatched(CommentAdded::class, function ($event) {
            return $event->commentId === 'comment-123' &&
                   $event->entityType === 'company' &&
                   $event->entityId === 'company-456' &&
                   $event->authorId === 'user-789' &&
                   $event->authorName === 'John Doe' &&
                   $event->comment === 'This is a comment' &&
                   $event->observers === ['user-111', 'user-222'];
        });
    }

    public function test_it_dispatches_status_changed_event(): void
    {
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Status change notification sent', $this->isType('array'));

        $this->service->notifyStatusChanged(
            'company',
            'company-456',
            'active',
            'inactive',
            'user-789',
            'John Doe',
            ['user-111', 'user-222']
        );

        Event::assertDispatched(StatusChanged::class, function ($event) {
            return $event->entityType === 'company' &&
                   $event->entityId === 'company-456' &&
                   $event->oldStatus === 'active' &&
                   $event->newStatus === 'inactive' &&
                   $event->changedByUserId === 'user-789' &&
                   $event->changedByName === 'John Doe' &&
                   $event->observers === ['user-111', 'user-222'];
        });
    }

    public function test_it_dispatches_opportunity_stage_changed_event(): void
    {
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Opportunity stage change notification sent', $this->isType('array'));

        $this->service->notifyOpportunityStageChanged(
            'opportunity-123',
            'stage-456',
            'stage-789',
            'Prospecting',
            'Demo',
            'user-789',
            'John Doe',
            ['user-111', 'user-222']
        );

        Event::assertDispatched(OpportunityStageChanged::class, function ($event) {
            return $event->opportunityId === 'opportunity-123' &&
                   $event->oldStageId === 'stage-456' &&
                   $event->newStageId === 'stage-789' &&
                   $event->oldStageName === 'Prospecting' &&
                   $event->newStageName === 'Demo' &&
                   $event->changedByUserId === 'user-789' &&
                   $event->changedByName === 'John Doe' &&
                   $event->observers === ['user-111', 'user-222'];
        });
    }

    public function test_it_handles_rate_limit_exceeded(): void
    {
        RateLimiter::shouldReceive('attempt')
            ->once()
            ->andReturn(false);

        $this->logger->expects($this->once())
            ->method('warning')
            ->with('Rate limit exceeded for task assignment notification', $this->isType('array'));

        $this->service->notifyTaskAssigned(
            'task-123',
            'user-456',
            'user-789',
            'Test Task',
            'Test Description'
        );

        Event::assertNotDispatched(TaskAssigned::class);
    }

    public function test_it_handles_null_due_date(): void
    {
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Task assignment notification sent', $this->isType('array'));

        $this->service->notifyTaskAssigned(
            'task-123',
            'user-456',
            'user-789',
            'Test Task',
            'Test Description',
            null
        );

        Event::assertDispatched(TaskAssigned::class, function ($event) {
            return $event->dueDate === null;
        });
    }
}
