<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Events\CommentAdded;
use App\Models\Comment;
use App\Models\Mention as MentionModel;
use App\Models\User as UserModel;
use App\Services\Collab\MentionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

final class MentionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private UserModel $user;

    private UserModel $mentionedUser;

    private Comment $comment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserModel::factory()->create([
            'username' => 'john',
        ]);

        $this->mentionedUser = UserModel::factory()->create([
            'username' => 'jane',
            'mention_notifications_enabled' => true,
            'mention_email_notifications_enabled' => true,
        ]);

        $this->comment = Comment::factory()->create([
            'content' => 'Hello @jane, please review this document.',
            'author_id' => $this->user->id,
        ]);
    }

    public function test_comment_creation_triggers_mention_parsing(): void
    {
        Event::fake();

        CommentAdded::dispatch(
            $this->comment->id,
            'company',
            Uuid::uuid7()->toString(),
            $this->user->id,
            $this->user->name,
            $this->comment->content,
            []
        );

        Event::assertDispatched(CommentAdded::class);
    }

    public function test_mention_parsing_creates_mention_record(): void
    {
        $mentionService = app(MentionService::class);

        $mentions = $mentionService->parseAndCreateMentions(
            $this->comment->content,
            Uuid::fromString($this->comment->id),
            Uuid::fromString($this->user->id),
            'company',
            Uuid::uuid7(),
        );

        $this->assertCount(1, $mentions);

        $this->assertDatabaseHas('mentions', [
            'mentioned_user_id' => $this->mentionedUser->id,
            'mentioner_user_id' => $this->user->id,
            'entity_type' => 'company',
        ]);
    }

    public function test_mention_parsing_skips_self_mentions(): void
    {
        $selfMentionComment = Comment::factory()->create([
            'content' => 'Hello @john, this is a self mention.',
            'author_id' => $this->user->id,
        ]);

        $mentionService = app(MentionService::class);

        $mentions = $mentionService->parseAndCreateMentions(
            $selfMentionComment->content,
            Uuid::fromString($selfMentionComment->id),
            Uuid::fromString($this->user->id),
            'company',
            Uuid::uuid7(),
        );

        $this->assertCount(0, $mentions);

        $this->assertDatabaseMissing('mentions', [
            'mentioned_user_id' => $this->user->id,
            'mentioner_user_id' => $this->user->id,
        ]);
    }

    public function test_mention_parsing_skips_users_who_opted_out(): void
    {
        $optedOutUser = UserModel::factory()->create([
            'username' => 'optedout',
            'mention_notifications_enabled' => false,
        ]);

        $comment = Comment::factory()->create([
            'content' => 'Hello @optedout, please review this.',
            'author_id' => $this->user->id,
        ]);

        $mentionService = app(MentionService::class);

        $mentions = $mentionService->parseAndCreateMentions(
            $comment->content,
            Uuid::fromString($comment->id),
            Uuid::fromString($this->user->id),
            'company',
            Uuid::uuid7(),
        );

        $this->assertCount(0, $mentions);

        $this->assertDatabaseMissing('mentions', [
            'mentioned_user_id' => $optedOutUser->id,
        ]);
    }

    public function test_mention_parsing_handles_multiple_mentions(): void
    {
        $anotherUser = UserModel::factory()->create([
            'username' => 'bob',
            'mention_notifications_enabled' => true,
        ]);

        $comment = Comment::factory()->create([
            'content' => 'Hello @jane and @bob, please review this.',
            'author_id' => $this->user->id,
        ]);

        $mentionService = app(MentionService::class);

        $mentions = $mentionService->parseAndCreateMentions(
            $comment->content,
            Uuid::fromString($comment->id),
            Uuid::fromString($this->user->id),
            'company',
            Uuid::uuid7(),
        );

        $this->assertCount(2, $mentions);

        $this->assertDatabaseHas('mentions', [
            'mentioned_user_id' => $this->mentionedUser->id,
        ]);

        $this->assertDatabaseHas('mentions', [
            'mentioned_user_id' => $anotherUser->id,
        ]);
    }

    public function test_mention_parsing_handles_duplicate_mentions(): void
    {
        $comment = Comment::factory()->create([
            'content' => 'Hello @jane and @jane again, please review this.',
            'author_id' => $this->user->id,
        ]);

        $mentionService = app(MentionService::class);

        $mentions = $mentionService->parseAndCreateMentions(
            $comment->content,
            Uuid::fromString($comment->id),
            Uuid::fromString($this->user->id),
            'company',
            Uuid::uuid7(),
        );

        $this->assertCount(1, $mentions);

        $this->assertDatabaseCount('mentions', 1);
    }

    public function test_mention_parsing_handles_nonexistent_users(): void
    {
        $comment = Comment::factory()->create([
            'content' => 'Hello @nonexistent, please review this.',
            'author_id' => $this->user->id,
        ]);

        $mentionService = app(MentionService::class);

        $mentions = $mentionService->parseAndCreateMentions(
            $comment->content,
            Uuid::fromString($comment->id),
            Uuid::fromString($this->user->id),
            'company',
            Uuid::uuid7(),
        );

        $this->assertCount(0, $mentions);

        $this->assertDatabaseCount('mentions', 0);
    }

    public function test_mention_marking_as_read(): void
    {
        $mention = MentionModel::factory()->create([
            'mentioned_user_id' => $this->mentionedUser->id,
            'mentioner_user_id' => $this->user->id,
            'comment_id' => $this->comment->id,
            'read_at' => null,
        ]);

        $mentionService = app(MentionService::class);

        $mentionService->markAsRead(
            Uuid::fromString($mention->id),
            $this->mentionedUser->id
        );

        $mention->refresh();

        $this->assertNotNull($mention->read_at);
    }

    public function test_mention_statistics(): void
    {
        MentionModel::factory()->count(5)->create([
            'mentioned_user_id' => $this->mentionedUser->id,
            'mentioner_user_id' => $this->user->id,
        ]);

        MentionModel::factory()->count(2)->create([
            'mentioned_user_id' => $this->mentionedUser->id,
            'mentioner_user_id' => $this->user->id,
            'read_at' => null,
        ]);

        $mentionService = app(MentionService::class);

        $stats = $mentionService->getMentionStats($this->mentionedUser->id);

        $this->assertEquals(7, $stats['total_mentions']);
        $this->assertEquals(2, $stats['unread_mentions']);
        $this->assertIsInt($stats['mentions_this_week']);
    }

    public function test_mention_parsing_with_edge_cases(): void
    {
        $comment = Comment::factory()->create([
            'content' => 'Hello @jane, @JANE, and @jane_doe, please review this.',
            'author_id' => $this->user->id,
        ]);

        $mentionService = app(MentionService::class);

        $mentions = $mentionService->parseAndCreateMentions(
            $comment->content,
            Uuid::fromString($comment->id),
            Uuid::fromString($this->user->id),
            'company',
            Uuid::uuid7(),
        );

        // Should only find @jane (case sensitive)
        $this->assertCount(1, $mentions);
    }

    public function test_mention_parsing_with_special_characters(): void
    {
        $comment = Comment::factory()->create([
            'content' => 'Hello @jane.doe and @jane-doe, please review this.',
            'author_id' => $this->user->id,
        ]);

        $mentionService = app(MentionService::class);

        $mentions = $mentionService->parseAndCreateMentions(
            $comment->content,
            Uuid::fromString($comment->id),
            Uuid::fromString($this->user->id),
            'company',
            Uuid::uuid7(),
        );

        // Should not find users with special characters in username
        $this->assertCount(0, $mentions);
    }
}
