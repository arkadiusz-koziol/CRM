<?php

declare(strict_types=1);

namespace App\Services\Collab;

use App\Domain\Collab\Entity\Comment;
use App\Events\CommentAdded;
use App\Interfaces\Repositories\CommentRepositoryInterface;
use App\Services\ActivityService;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class CommentService
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private LoggerInterface $logger,
        private ActivityService $activityService,
    ) {}

    public function createComment(
        string $content,
        string $commentableType,
        string $commentableId,
        int $authorId,
        ?string $parentId = null,
        bool $isPrivate = false,
    ): string {
        $this->validateCommentableType($commentableType);

        $mentions = $this->extractMentions($content);
        $contentHtml = $this->processMarkdown($content);

        $comment = Comment::create(
            id: Str::uuid()->toString(),
            content: $content,
            commentableType: $commentableType,
            commentableId: $commentableId,
            authorId: $authorId,
            parentId: $parentId,
            isPrivate: $isPrivate,
            mentions: $mentions,
        );

        $this->commentRepository->save($comment);

        // Log activity
        $this->activityService->logActivity(
            'comment_created',
            'User', // TODO: Get actual user name
            'user@example.com', // TODO: Get actual user email
            $commentableType,
            $commentableId
        );

        // Dispatch event for real-time notifications
        event(new CommentAdded(
            commentId: $comment->id(),
            entityType: $commentableType,
            entityId: $commentableId,
            authorId: (string) $authorId,
            authorName: 'User', // TODO: Get actual user name
            comment: $content,
            observers: $this->getEntityObservers($commentableType, $commentableId)
        ));

        $this->logger->info('Comment created', [
            'comment_id' => $comment->id(),
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'author_id' => $authorId,
        ]);

        return $comment->id();
    }

    public function updateComment(string $id, string $content, int $userId): void
    {
        $comment = $this->commentRepository->findById($id);

        if (! $comment) {
            throw new \RuntimeException("Comment with ID {$id} not found.");
        }

        if ($comment->authorId() !== $userId) {
            throw new \RuntimeException('You can only edit your own comments.');
        }

        $mentions = $this->extractMentions($content);
        $contentHtml = $this->processMarkdown($content);

        $comment->updateContent($content, $contentHtml);
        $comment->updateMentions($mentions);

        $this->commentRepository->save($comment);

        // Log activity
        $this->activityService->logActivity(
            'comment_updated',
            'User', // TODO: Get actual user name
            'user@example.com', // TODO: Get actual user email
            $comment->commentableType(),
            $comment->commentableId()
        );

        $this->logger->info('Comment updated', [
            'comment_id' => $id,
            'author_id' => $userId,
        ]);
    }

    public function deleteComment(string $id, int $userId): void
    {
        $comment = $this->commentRepository->findById($id);

        if (! $comment) {
            throw new \RuntimeException("Comment with ID {$id} not found.");
        }

        if ($comment->authorId() !== $userId) {
            throw new \RuntimeException('You can only delete your own comments.');
        }

        $comment->markAsDeleted();
        $this->commentRepository->save($comment);

        // Log activity
        $this->activityService->logActivity(
            'comment_deleted',
            'User', // TODO: Get actual user name
            'user@example.com', // TODO: Get actual user email
            $comment->commentableType(),
            $comment->commentableId()
        );

        $this->logger->info('Comment deleted', [
            'comment_id' => $id,
            'author_id' => $userId,
        ]);
    }

    public function getComments(string $commentableType, string $commentableId, int $userId, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $this->validateCommentableType($commentableType);

        return $this->commentRepository->findVisibleForUser($commentableType, $commentableId, $userId, $perPage);
    }

    public function getReplies(string $parentId, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->commentRepository->findReplies($parentId, $perPage);
    }

    public function getCommentById(string $id): ?Comment
    {
        return $this->commentRepository->findById($id);
    }

    private function validateCommentableType(string $type): void
    {
        $allowedTypes = [
            'App\\Models\\Task',
            'App\\Models\\Company',
            'App\\Models\\Contact',
            'App\\Models\\Opportunity',
            'App\\Models\\Estate',
        ];

        if (! in_array($type, $allowedTypes, true)) {
            throw new \RuntimeException("Invalid commentable type: {$type}");
        }
    }

    private function extractMentions(string $content): array
    {
        preg_match_all('/@(\w+)/', $content, $matches);

        return $matches[1] ?? [];
    }

    private function processMarkdown(string $content): string
    {
        // Basic markdown processing
        $html = $content;

        // Bold
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);

        // Italic
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);

        // Links (with URL validation)
        $html = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function ($matches) {
            $text = $matches[1];
            $url = $matches[2];

            // Basic URL validation
            if (filter_var($url, FILTER_VALIDATE_URL) || preg_match('/^\/[^\/]/', $url)) {
                return '<a href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'</a>';
            }

            return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        }, $html);

        // Line breaks
        $html = nl2br($html);

        // Mentions (sanitized)
        $html = preg_replace_callback('/@(\w+)/', function ($matches) {
            return '<span class="mention">@'.htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8').'</span>';
        }, $html);

        // XSS sanitization - remove any remaining dangerous tags
        $html = strip_tags($html, '<strong><em><a><br><span>');

        // Additional XSS protection
        $html = htmlspecialchars($html, ENT_QUOTES, 'UTF-8');

        // Restore allowed tags
        $html = str_replace(
            ['&lt;strong&gt;', '&lt;/strong&gt;', '&lt;em&gt;', '&lt;/em&gt;', '&lt;a&gt;', '&lt;/a&gt;', '&lt;br&gt;', '&lt;span&gt;', '&lt;/span&gt;'],
            ['<strong>', '</strong>', '<em>', '</em>', '<a>', '</a>', '<br>', '<span>', '</span>'],
            $html
        );

        return $html;
    }

    private function getEntityObservers(string $commentableType, string $commentableId): array
    {
        // TODO: Implement proper observer logic based on entity type
        // For now, return empty array - this should be enhanced based on business rules
        return [];
    }
}
