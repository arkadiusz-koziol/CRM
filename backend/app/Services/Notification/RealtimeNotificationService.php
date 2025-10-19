<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Events\CommentAdded;
use App\Events\OpportunityStageChanged;
use App\Events\StatusChanged;
use App\Events\TaskAssigned;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Psr\Log\LoggerInterface;

final class RealtimeNotificationService
{
    private const RATE_LIMIT_KEY = 'realtime_notifications';

    private const RATE_LIMIT_ATTEMPTS = 100;

    private const RATE_LIMIT_DECAY_MINUTES = 1;

    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function notifyTaskAssigned(
        string $taskId,
        string $assignedToUserId,
        string $assignedByUserId,
        string $taskTitle,
        string $taskDescription,
        ?string $dueDate = null
    ): void {
        if (! $this->checkRateLimit($assignedByUserId)) {
            $this->logger->warning('Rate limit exceeded for task assignment notification', [
                'assigned_by_user_id' => $assignedByUserId,
                'task_id' => $taskId,
            ]);

            return;
        }

        TaskAssigned::dispatch(
            $taskId,
            $assignedToUserId,
            $assignedByUserId,
            $taskTitle,
            $taskDescription,
            $dueDate
        );

        $this->logger->info('Task assignment notification sent', [
            'task_id' => $taskId,
            'assigned_to_user_id' => $assignedToUserId,
            'assigned_by_user_id' => $assignedByUserId,
        ]);
    }

    public function notifyCommentAdded(
        string $commentId,
        string $entityType,
        string $entityId,
        string $authorId,
        string $authorName,
        string $comment,
        array $observers = []
    ): void {
        if (! $this->checkRateLimit($authorId)) {
            $this->logger->warning('Rate limit exceeded for comment notification', [
                'author_id' => $authorId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]);

            return;
        }

        CommentAdded::dispatch(
            $commentId,
            $entityType,
            $entityId,
            $authorId,
            $authorName,
            $comment,
            $observers
        );

        $this->logger->info('Comment notification sent', [
            'comment_id' => $commentId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'author_id' => $authorId,
        ]);
    }

    public function notifyStatusChanged(
        string $entityType,
        string $entityId,
        string $oldStatus,
        string $newStatus,
        string $changedByUserId,
        string $changedByName,
        array $observers = []
    ): void {
        if (! $this->checkRateLimit($changedByUserId)) {
            $this->logger->warning('Rate limit exceeded for status change notification', [
                'changed_by_user_id' => $changedByUserId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]);

            return;
        }

        StatusChanged::dispatch(
            $entityType,
            $entityId,
            $oldStatus,
            $newStatus,
            $changedByUserId,
            $changedByName,
            $observers
        );

        $this->logger->info('Status change notification sent', [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_user_id' => $changedByUserId,
        ]);
    }

    public function notifyOpportunityStageChanged(
        string $opportunityId,
        string $oldStageId,
        string $newStageId,
        string $oldStageName,
        string $newStageName,
        string $changedByUserId,
        string $changedByName,
        array $observers = []
    ): void {
        if (! $this->checkRateLimit($changedByUserId)) {
            $this->logger->warning('Rate limit exceeded for opportunity stage change notification', [
                'changed_by_user_id' => $changedByUserId,
                'opportunity_id' => $opportunityId,
            ]);

            return;
        }

        OpportunityStageChanged::dispatch(
            $opportunityId,
            $oldStageId,
            $newStageId,
            $oldStageName,
            $newStageName,
            $changedByUserId,
            $changedByName,
            $observers
        );

        $this->logger->info('Opportunity stage change notification sent', [
            'opportunity_id' => $opportunityId,
            'old_stage_id' => $oldStageId,
            'new_stage_id' => $newStageId,
            'changed_by_user_id' => $changedByUserId,
        ]);
    }

    public function getObserversForEntity(string $entityType, string $entityId): array
    {
        $cacheKey = "observers.{$entityType}.{$entityId}";

        return Cache::remember($cacheKey, 300, function () use ($entityType, $entityId) {
            return match ($entityType) {
                'company' => $this->getCompanyObservers($entityId),
                'contact' => $this->getContactObservers($entityId),
                'opportunity' => $this->getOpportunityObservers($entityId),
                'task' => $this->getTaskObservers($entityId),
                default => [],
            };
        });
    }

    private function checkRateLimit(string $userId): bool
    {
        $key = self::RATE_LIMIT_KEY.'.'.$userId;

        return RateLimiter::attempt(
            $key,
            self::RATE_LIMIT_ATTEMPTS,
            function () {
                // Rate limit not exceeded
            },
            self::RATE_LIMIT_DECAY_MINUTES * 60
        );
    }

    private function getCompanyObservers(string $companyId): array
    {
        // Get users assigned to the company
        return User::whereHas('companies', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->pluck('id')->toArray();
    }

    private function getContactObservers(string $contactId): array
    {
        // Get users who have access to the contact
        return User::whereHas('contacts', function ($query) use ($contactId) {
            $query->where('contact_id', $contactId);
        })->pluck('id')->toArray();
    }

    private function getOpportunityObservers(string $opportunityId): array
    {
        // Get users who have access to the opportunity
        return User::whereHas('opportunities', function ($query) use ($opportunityId) {
            $query->where('opportunity_id', $opportunityId);
        })->pluck('id')->toArray();
    }

    private function getTaskObservers(string $taskId): array
    {
        // Get users assigned to the task and the creator
        return User::whereHas('tasks', function ($query) use ($taskId) {
            $query->where('task_id', $taskId);
        })->orWhereHas('createdTasks', function ($query) use ($taskId) {
            $query->where('task_id', $taskId);
        })->pluck('id')->toArray();
    }
}
