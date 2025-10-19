<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Collab;

use App\Domain\Collab\Entity\Comment;
use App\Interfaces\Repositories\CommentRepositoryInterface;
use App\Services\Collab\CommentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class CommentServiceTest extends TestCase
{
    private CommentService $service;

    private CommentRepositoryInterface $repository;

    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(CommentRepositoryInterface::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->service = new CommentService($this->repository, $this->logger);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_comment_successfully(): void
    {
        $content = 'Test comment';
        $commentableType = 'App\\Models\\Task';
        $commentableId = 'test-task-id';
        $authorId = 1;

        $this->repository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Comment::class));

        $this->logger->shouldReceive('info')
            ->once()
            ->with('Comment created', Mockery::type('array'));

        $commentId = $this->service->createComment(
            $content,
            $commentableType,
            $commentableId,
            $authorId
        );

        $this->assertIsString($commentId);
    }

    public function test_it_throws_exception_for_invalid_commentable_type(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid commentable type: InvalidType');

        $this->service->createComment(
            'Test comment',
            'InvalidType',
            'test-id',
            1
        );
    }

    public function test_it_updates_comment_successfully(): void
    {
        $commentId = 'test-comment-id';
        $content = 'Updated comment';
        $userId = 1;

        $comment = Comment::create(
            id: $commentId,
            content: 'Original content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-task-id',
            authorId: $userId,
        );

        $this->repository->shouldReceive('findById')
            ->with($commentId)
            ->andReturn($comment);

        $this->repository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Comment::class));

        $this->logger->shouldReceive('info')
            ->once()
            ->with('Comment updated', Mockery::type('array'));

        $this->service->updateComment($commentId, $content, $userId);
    }

    public function test_it_throws_exception_when_updating_nonexistent_comment(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Comment with ID test-id not found.');

        $this->repository->shouldReceive('findById')
            ->with('test-id')
            ->andReturn(null);

        $this->service->updateComment('test-id', 'Updated content', 1);
    }

    public function test_it_throws_exception_when_updating_other_users_comment(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You can only edit your own comments.');

        $comment = Comment::create(
            id: 'test-id',
            content: 'Original content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-task-id',
            authorId: 2, // Different user
        );

        $this->repository->shouldReceive('findById')
            ->with('test-id')
            ->andReturn($comment);

        $this->service->updateComment('test-id', 'Updated content', 1);
    }

    public function test_it_deletes_comment_successfully(): void
    {
        $commentId = 'test-comment-id';
        $userId = 1;

        $comment = Comment::create(
            id: $commentId,
            content: 'Original content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-task-id',
            authorId: $userId,
        );

        $this->repository->shouldReceive('findById')
            ->with($commentId)
            ->andReturn($comment);

        $this->repository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Comment::class));

        $this->logger->shouldReceive('info')
            ->once()
            ->with('Comment deleted', Mockery::type('array'));

        $this->service->deleteComment($commentId, $userId);
    }

    public function test_it_throws_exception_when_deleting_other_users_comment(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You can only delete your own comments.');

        $comment = Comment::create(
            id: 'test-id',
            content: 'Original content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-task-id',
            authorId: 2, // Different user
        );

        $this->repository->shouldReceive('findById')
            ->with('test-id')
            ->andReturn($comment);

        $this->service->deleteComment('test-id', 1);
    }

    public function test_it_gets_comments_for_commentable(): void
    {
        $commentableType = 'App\\Models\\Task';
        $commentableId = 'test-task-id';
        $userId = 1;
        $perPage = 15;

        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository->shouldReceive('findVisibleForUser')
            ->with($commentableType, $commentableId, $userId, $perPage)
            ->andReturn($paginator);

        $result = $this->service->getComments($commentableType, $commentableId, $userId, $perPage);

        $this->assertSame($paginator, $result);
    }

    public function test_it_gets_replies_for_comment(): void
    {
        $parentId = 'parent-comment-id';
        $perPage = 15;

        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository->shouldReceive('findReplies')
            ->with($parentId, $perPage)
            ->andReturn($paginator);

        $result = $this->service->getReplies($parentId, $perPage);

        $this->assertSame($paginator, $result);
    }

    public function test_it_gets_comment_by_id(): void
    {
        $commentId = 'test-comment-id';
        $comment = Comment::create(
            id: $commentId,
            content: 'Test content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-task-id',
            authorId: 1,
        );

        $this->repository->shouldReceive('findById')
            ->with($commentId)
            ->andReturn($comment);

        $result = $this->service->getCommentById($commentId);

        $this->assertSame($comment, $result);
    }
}
