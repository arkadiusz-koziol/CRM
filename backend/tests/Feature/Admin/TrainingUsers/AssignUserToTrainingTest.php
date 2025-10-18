<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingUsers;

use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class AssignUserToTrainingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->admin->givePermissionTo('training.user.assign');
    }

    public function test_admin_can_assign_user_to_training(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users", [
            'user_id' => $user->id,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => __('app.training_user.assigned_successfully'),
                'data' => [
                    'training_id' => $training->id,
                    'user_id' => $user->id,
                ],
            ]);

        $this->assertDatabaseHas('training_user', [
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_cannot_assign_same_user_twice(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        // First assignment
        $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users", [
            'user_id' => $user->id,
        ]);

        // Second assignment should fail
        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users", [
            'user_id' => $user->id,
        ]);

        $response->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson(['message' => 'User is already assigned to this training']);
    }

    public function test_admin_cannot_assign_user_to_nonexistent_training(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/trainings/99999/users', [
            'user_id' => $user->id,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Training not found']);
    }

    public function test_admin_cannot_assign_nonexistent_user_to_training(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users", [
            'user_id' => 99999,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['user_id']);
    }

    public function test_unauthenticated_user_cannot_assign_user_to_training(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $response = $this->postJson("/api/v1/admin/trainings/{$training->id}/users", [
            'user_id' => $user->id,
        ]);

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_assign_user_to_training(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('training.create'); // Has create permission but not assign
        $training = Training::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/v1/admin/trainings/{$training->id}/users", [
            'user_id' => $targetUser->id,
        ]);

        $response->assertForbidden();
    }

    public function test_assign_user_to_training_requires_user_id(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users", []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['user_id']);
    }

    public function test_assign_user_to_training_handles_internal_server_error(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        // Mock the service to throw an exception
        $this->mock(\App\Services\TrainingUserService::class, function ($mock) use ($training, $user) {
            $mock->shouldReceive('assignUserToTraining')
                ->once()
                ->with($training->id, $user->id)
                ->andThrow(new \RuntimeException('Simulated internal error'));
        });

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users", [
            'user_id' => $user->id,
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }
}
