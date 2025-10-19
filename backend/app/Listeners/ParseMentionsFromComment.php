<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CommentAdded;
use App\Services\Collab\MentionService;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class ParseMentionsFromComment
{
    public function __construct(
        private readonly MentionService $mentionService,
        private readonly LoggerInterface $logger,
    ) {}

    public function handle(CommentAdded $event): void
    {
        try {
            $this->mentionService->parseAndCreateMentions(
                $event->comment,
                Uuid::fromString($event->commentId),
                (int) $event->authorId,
                $event->entityType,
                Uuid::fromString($event->entityId),
            );

            $this->logger->info('Mentions parsed from comment', [
                'comment_id' => $event->commentId,
                'entity_type' => $event->entityType,
                'entity_id' => $event->entityId,
                'author_id' => $event->authorId,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to parse mentions from comment', [
                'comment_id' => $event->commentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
