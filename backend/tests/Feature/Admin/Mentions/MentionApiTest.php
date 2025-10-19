<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Mentions;

use App\Models\Comment;
use App\Models\Mention;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class MentionApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private User $mentionedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->mentionedUser = User::factory()->create([
            'username' => 'johndoe',
            'mention_notifications_enabled' => true,
            'mention_email_notifications_enabled' => true,
        ]);

        Sanctum::actingAs($this->user);
    }

    public function test_can_get_mentions_for_user(): void
    {
        $comment = Comment::factory()->create();

        $mention = Mention::factory()->create([
            'mentioned_user_id' => $this->user->id,
            'mentioner_user_id' => $this->mentionedUser->id,
            'comment_id' => $comment->id,
        ]);

        $response = $this->getJson('/api/v1/admin/mentions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'data' => [
                            'type',
                            'id',
                            'attributes' => [
                                'comment_id',
                                'mentioned_user_id',
                                'mentioner_user_id',
                                'entity_type',
                                'entity_id',
                                'created_at',
                                'notified_at',
                                'read_at',
                                'is_notified',
                                'is_read',
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_mark_mention_as_read(): void
    {
        $comment = Comment::factory()->create();

        $mention = Mention::factory()->create([
            'mentioned_user_id' => $this->user->id,
            'mentioner_user_id' => $this->mentionedUser->id,
            'comment_id' => $comment->id,
            'read_at' => null,
        ]);

        $response = $this->postJson("/api/v1/admin/mentions/{$mention->id}/read");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Mention marked as read',
            ]);

        $this->assertDatabaseHas('mentions', [
            'id' => $mention->id,
            'read_at' => now()->toDateTimeString(),
        ]);
    }

    public function test_cannot_mark_other_users_mention_as_read(): void
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create();

        $mention = Mention::factory()->create([
            'mentioned_user_id' => $otherUser->id,
            'mentioner_user_id' => $this->mentionedUser->id,
            'comment_id' => $comment->id,
        ]);

        $response = $this->postJson("/api/v1/admin/mentions/{$mention->id}/read");

        $response->assertStatus(200);

        $this->assertDatabaseHas('mentions', [
            'id' => $mention->id,
            'read_at' => null,
        ]);
    }

    public function test_can_get_mention_stats(): void
    {
        $comment = Comment::factory()->create();

        Mention::factory()->count(5)->create([
            'mentioned_user_id' => $this->user->id,
            'mentioner_user_id' => $this->mentionedUser->id,
            'comment_id' => $comment->id,
        ]);

        Mention::factory()->count(2)->create([
            'mentioned_user_id' => $this->user->id,
            'mentioner_user_id' => $this->mentionedUser->id,
            'comment_id' => $comment->id,
            'read_at' => null,
        ]);

        $response = $this->getJson('/api/v1/admin/mentions/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_mentions',
                    'unread_mentions',
                    'mentions_this_week',
                ],
            ]);

        $this->assertEquals(7, $response->json('data.total_mentions'));
        $this->assertEquals(2, $response->json('data.unread_mentions'));
    }

    public function test_requires_authentication(): void
    {
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/v1/admin/mentions');

        $response->assertStatus(401);
    }

    public function test_requires_mention_view_permission(): void
    {
        $this->user->revokePermissionTo('mention.view');

        $response = $this->getJson('/api/v1/admin/mentions');

        $response->assertStatus(403);
    }

    public function test_requires_mention_update_permission_for_mark_as_read(): void
    {
        $comment = Comment::factory()->create();
        $mention = Mention::factory()->create([
            'mentioned_user_id' => $this->user->id,
            'comment_id' => $comment->id,
        ]);

        $this->user->revokePermissionTo('mention.update');

        $response = $this->postJson("/api/v1/admin/mentions/{$mention->id}/read");

        $response->assertStatus(403);
    }

    public function test_handles_invalid_mention_id(): void
    {
        $response = $this->postJson('/api/v1/admin/mentions/invalid-id/read');

        $response->assertStatus(200);
    }

    public function test_pagination_works_correctly(): void
    {
        $comment = Comment::factory()->create();

        Mention::factory()->count(25)->create([
            'mentioned_user_id' => $this->user->id,
            'mentioner_user_id' => $this->mentionedUser->id,
            'comment_id' => $comment->id,
        ]);

        $response = $this->getJson('/api/v1/admin/mentions?limit=10');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
    }

    public function test_respects_limit_parameter(): void
    {
        $comment = Comment::factory()->create();

        Mention::factory()->count(5)->create([
            'mentioned_user_id' => $this->user->id,
            'mentioner_user_id' => $this->mentionedUser->id,
            'comment_id' => $comment->id,
        ]);

        $response = $this->getJson('/api/v1/admin/mentions?limit=3');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_enforces_maximum_limit(): void
    {
        $comment = Comment::factory()->create();

        Mention::factory()->count(5)->create([
            'mentioned_user_id' => $this->user->id,
            'mentioner_user_id' => $this->mentionedUser->id,
            'comment_id' => $comment->id,
        ]);

        $response = $this->getJson('/api/v1/admin/mentions?limit=200');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json('data'));
    }
}
