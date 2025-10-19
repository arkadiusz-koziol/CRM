<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Collab\Entity;

use App\Domain\Collab\Entity\Comment;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase
{
    public function test_it_creates_comment_with_all_required_fields(): void
    {
        $id = 'test-comment-id';
        $content = 'This is a test comment';
        $commentableType = 'App\\Models\\Task';
        $commentableId = 'test-task-id';
        $authorId = 1;
        $now = Carbon::now();

        $comment = Comment::create(
            id: $id,
            content: $content,
            commentableType: $commentableType,
            commentableId: $commentableId,
            authorId: $authorId,
        );

        $this->assertEquals($id, $comment->id());
        $this->assertEquals($content, $comment->content());
        $this->assertEquals($commentableType, $comment->commentableType());
        $this->assertEquals($commentableId, $comment->commentableId());
        $this->assertEquals($authorId, $comment->authorId());
        $this->assertNull($comment->parentId());
        $this->assertFalse($comment->isPrivate());
        $this->assertNull($comment->mentions());
        $this->assertFalse($comment->isReply());
        $this->assertFalse($comment->isDeleted());
    }

    public function test_it_creates_comment_with_optional_fields(): void
    {
        $id = 'test-comment-id';
        $content = 'This is a test comment';
        $commentableType = 'App\\Models\\Company';
        $commentableId = 'test-company-id';
        $authorId = 2;
        $parentId = 'parent-comment-id';
        $isPrivate = true;
        $mentions = ['@user1', '@user2'];

        $comment = Comment::create(
            id: $id,
            content: $content,
            commentableType: $commentableType,
            commentableId: $commentableId,
            authorId: $authorId,
            parentId: $parentId,
            isPrivate: $isPrivate,
            mentions: $mentions,
        );

        $this->assertEquals($parentId, $comment->parentId());
        $this->assertTrue($comment->isPrivate());
        $this->assertEquals($mentions, $comment->mentions());
        $this->assertTrue($comment->isReply());
    }

    public function test_it_updates_content(): void
    {
        $comment = Comment::create(
            id: 'test-id',
            content: 'Original content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-id',
            authorId: 1,
        );

        $newContent = 'Updated content';
        $newContentHtml = '<p>Updated content</p>';

        $comment->updateContent($newContent, $newContentHtml);

        $this->assertEquals($newContent, $comment->content());
        $this->assertEquals($newContentHtml, $comment->contentHtml());
    }

    public function test_it_updates_visibility(): void
    {
        $comment = Comment::create(
            id: 'test-id',
            content: 'Test content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-id',
            authorId: 1,
        );

        $comment->updateVisibility(true);

        $this->assertTrue($comment->isPrivate());
    }

    public function test_it_updates_mentions(): void
    {
        $comment = Comment::create(
            id: 'test-id',
            content: 'Test content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-id',
            authorId: 1,
        );

        $mentions = ['@user1', '@user2'];
        $comment->updateMentions($mentions);

        $this->assertEquals($mentions, $comment->mentions());
    }

    public function test_it_marks_as_deleted(): void
    {
        $comment = Comment::create(
            id: 'test-id',
            content: 'Test content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-id',
            authorId: 1,
        );

        $comment->markAsDeleted();

        $this->assertTrue($comment->isDeleted());
        $this->assertNotNull($comment->deletedAt());
    }

    public function test_it_restores_deleted_comment(): void
    {
        $comment = Comment::create(
            id: 'test-id',
            content: 'Test content',
            commentableType: 'App\\Models\\Task',
            commentableId: 'test-id',
            authorId: 1,
        );

        $comment->markAsDeleted();
        $comment->restore();

        $this->assertFalse($comment->isDeleted());
        $this->assertNull($comment->deletedAt());
    }
}
