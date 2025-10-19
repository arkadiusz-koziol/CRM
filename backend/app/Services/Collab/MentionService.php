<?php

declare(strict_types=1);

namespace App\Services\Collab;

use App\Domain\Collab\Entity\Mention as MentionEntity;
use App\Interfaces\Repositories\MentionRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\RealtimeNotificationServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class MentionService
{
    public function __construct(
        private readonly MentionRepositoryInterface $mentionRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly RealtimeNotificationServiceInterface $notificationService,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Parse mentions from comment content and create mention records.
     */
    public function parseAndCreateMentions(
        string $content,
        UuidInterface $commentId,
        int $mentionerUserId,
        string $entityType,
        UuidInterface $entityId,
    ): Collection {
        $mentions = $this->extractMentions($content);

        if ($mentions->isEmpty()) {
            return collect();
        }

        $createdMentions = collect();

        foreach ($mentions as $username) {
            $user = $this->userRepository->findByUsername($username);

            if (! $user) {
                $this->logger->warning('Mentioned user not found', [
                    'username' => $username,
                    'comment_id' => $commentId->toString(),
                ]);

                continue;
            }

            // Skip self-mentions
            if ($user->intId() === $mentionerUserId) {
                continue;
            }

            // Check if user has opted out of mentions
            if ($user->hasOptedOutOfMentions()) {
                $this->logger->info('User has opted out of mentions', [
                    'user_id' => $user->intId(),
                    'username' => $username,
                ]);

                continue;
            }

            // Check for duplicate mentions in the same comment
            $existingMention = $this->mentionRepository->findByCommentAndUser($commentId, $user->intId());
            if ($existingMention) {
                continue;
            }

            $mention = MentionEntity::create(
                Uuid::uuid7(),
                $commentId,
                $user->intId(),
                $mentionerUserId,
                $entityType,
                $entityId,
                Carbon::now(),
            );

            $this->mentionRepository->save($mention);
            $createdMentions->push($mention);

            // Send notifications
            $this->sendMentionNotifications($mention);
        }

        return $createdMentions;
    }

    /**
     * Extract @username mentions from content.
     */
    private function extractMentions(string $content): Collection
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $content, $matches);

        return collect($matches[1] ?? [])
            ->unique()
            ->filter(fn (string $username) => ! empty(trim($username)));
    }

    /**
     * Send WebSocket and email notifications for mentions.
     */
    private function sendMentionNotifications(MentionEntity $mention): void
    {
        try {
            // Get users to send UUID to notification service
            $mentionedUser = $this->userRepository->findByIntId($mention->mentionedUserId());
            $mentionerUser = $this->userRepository->findByIntId($mention->mentionerUserId());

            if (! $mentionedUser || ! $mentionerUser) {
                $this->logger->warning('User not found for mention notification', [
                    'mentioned_user_id' => $mention->mentionedUserId(),
                    'mentioner_user_id' => $mention->mentionerUserId(),
                ]);

                return;
            }

            // Send WebSocket notification
            $this->notificationService->sendMentionNotification(
                $mentionedUser->id(),
                $mentionerUser->id(),
                $mention->entityType(),
                $mention->entityId(),
                $mention->commentId(),
            );

            // Send email notification (if user hasn't opted out of email notifications)
            if (! $mentionedUser->hasOptedOutOfMentionEmails()) {
                $this->notificationService->sendMentionEmail(
                    $mentionedUser->id(),
                    $mentionerUser->id(),
                    $mention->entityType(),
                    $mention->entityId(),
                    $mention->commentId(),
                );
            }

            // Mark as notified
            $notifiedMention = $mention->markAsNotified(Carbon::now());
            $this->mentionRepository->save($notifiedMention);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send mention notifications', [
                'mention_id' => $mention->id()->toString(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark mention as read.
     */
    public function markAsRead(UuidInterface $mentionId, int $userId): void
    {
        $mention = $this->mentionRepository->findByUuid($mentionId);

        if (! $mention) {
            return;
        }

        // Only the mentioned user can mark as read
        if ($mention->mentionedUserId() !== $userId) {
            return;
        }

        $readMention = $mention->markAsRead(Carbon::now());
        $this->mentionRepository->save($readMention);
    }

    /**
     * Get mentions for a user.
     */
    public function getMentionsForUser(int $userId, int $limit = 50): Collection
    {
        return $this->mentionRepository->findByMentionedUser($userId, $limit);
    }

    /**
     * Get mention statistics.
     */
    public function getMentionStats(int $userId): array
    {
        return [
            'total_mentions' => $this->mentionRepository->countByMentionedUser($userId),
            'unread_mentions' => $this->mentionRepository->countUnreadByMentionedUser($userId),
            'mentions_this_week' => $this->mentionRepository->countByMentionedUserSince($userId, Carbon::now()->subWeek()),
        ];
    }
}
