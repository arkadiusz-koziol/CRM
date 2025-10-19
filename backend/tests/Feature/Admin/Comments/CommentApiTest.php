<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Comments;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class CommentApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_it_creates_comment_successfully(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('comment.create');

        $commentData = [
            'content' => 'This is a test comment',
            'commentable_type' => 'App\\Models\\Task',
            'commentable_id' => '550e8400-e29b-41d4-a716-446655440000',
            'is_private' => false,
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/v1/admin/comments', $commentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'content',
                        'content_html',
                        'commentable_type',
                        'commentable_id',
                        'author_id',
                        'parent_id',
                        'is_private',
                        'mentions',
                        'is_reply',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment',
            'commentable_type' => 'App\\Models\\Task',
            'commentable_id' => 'test-task-id',
            'author_id' => $user->id,
        ]);
    }

    public function test_it_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('comment.create');

        $response = $this->actingAs($user)
            ->postJson('/api/v1/admin/comments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content', 'commentable_type', 'commentable_id']);
    }

    public function test_it_validates_commentable_type(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('comment.create');

        $commentData = [
            'content' => 'Test comment',
            'commentable_type' => 'InvalidType',
            'commentable_id' => 'test-id',
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/v1/admin/comments', $commentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['commentable_type']);
    }

    public function test_it_requires_authentication(): void
    {
        $commentData = [
            'content' => 'Test comment',
            'commentable_type' => 'App\\Models\\Task',
            'commentable_id' => 'test-id',
        ];

        $response = $this->postJson('/api/v1/admin/comments', $commentData);

        $response->assertStatus(401);
    }

    public function test_it_requires_permission(): void
    {
        $user = User::factory()->create();

        $commentData = [
            'content' => 'Test comment',
            'commentable_type' => 'App\\Models\\Task',
            'commentable_id' => 'test-id',
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/v1/admin/comments', $commentData);

        $response->assertStatus(403);
    }

    public function test_it_lists_comments_for_commentable(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('comment.view');

        Comment::factory()->create([
            'commentable_type' => 'App\\Models\\Task',
            'commentable_id' => 'test-task-id',
            'author_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/admin/comments?commentable_type=App\\Models\\Task&commentable_id=test-task-id');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes',
                    ],
                ],
            ]);
    }

    public function test_it_shows_comment(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('comment.view');

        $comment = Comment::factory()->create([
            'author_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/v1/admin/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes',
                ],
            ]);
    }

    public function test_it_updates_comment(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['comment.create', 'comment.update']);

        $comment = Comment::factory()->create([
            'author_id' => $user->id,
        ]);

        $updateData = [
            'content' => 'Updated comment content',
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/v1/admin/comments/{$comment->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment content',
        ]);
    }

    public function test_it_deletes_comment(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['comment.create', 'comment.delete']);

        $comment = Comment::factory()->create([
            'author_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/admin/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment deleted successfully.']);

        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    public function test_it_gets_replies_for_comment(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('comment.view');

        $parentComment = Comment::factory()->create([
            'author_id' => $user->id,
        ]);

        Comment::factory()->reply()->create([
            'parent_id' => $parentComment->id,
            'author_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/v1/admin/comments/{$parentComment->id}/replies");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes',
                    ],
                ],
            ]);
    }

    public function test_it_returns_404_for_nonexistent_comment(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('comment.view');

        $response = $this->actingAs($user)
            ->getJson('/api/v1/admin/comments/nonexistent-id');

        $response->assertStatus(404);
    }

    public function test_it_prevents_updating_other_users_comment(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo('comment.update');

        $comment = Comment::factory()->create([
            'author_id' => $otherUser->id,
        ]);

        $updateData = [
            'content' => 'Updated comment content',
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/v1/admin/comments/{$comment->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_it_prevents_deleting_other_users_comment(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo('comment.delete');

        $comment = Comment::factory()->create([
            'author_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/admin/comments/{$comment->id}");

        $response->assertStatus(403);
    }
}
