<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingUsers;

use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class AssignSelectedUsersToTrainingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->admin->givePermissionTo('training.user.assign_selected');
    }

    public function test_admin_can_assign_selected_users_to_training(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-selected", [
            'user_ids' => $userIds,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => __('app.training_user.selected_users_assigned_successfully'),
                'data' => [
                    'training_id' => $training->id,
                    'user_ids' => $userIds,
                    'assigned_count' => 3,
                ],
            ]);

        foreach ($users as $user) {
            $this->assertDatabaseHas('training_user', [
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function test_admin_cannot_assign_selected_users_to_nonexistent_training(): void
    {
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/trainings/99999/users/assign-selected', [
            'user_ids' => $userIds,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Training not found']);
    }

    public function test_assign_selected_users_requires_user_ids(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-selected", []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['user_ids']);
    }

    public function test_assign_selected_users_requires_non_empty_user_ids(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-selected", [
            'user_ids' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['user_ids']);
    }

    public function test_assign_selected_users_validates_user_ids_exist(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-selected", [
            'user_ids' => [99999, 99998],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['user_ids.0', 'user_ids.1']);
    }

    public function test_assign_selected_users_handles_internal_server_error(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck('id')->toArray();

        // Mock the service to throw an exception
        $this->mock(\App\Services\TrainingUserService::class, function ($mock) use ($training, $userIds) {
            $mock->shouldReceive('assignSelectedUsersToTraining')
                ->once()
                ->with($training->id, $userIds)
                ->andThrow(new \RuntimeException('Simulated internal error'));
        });

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-selected", [
            'user_ids' => $userIds,
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }

    public function test_unauthenticated_user_cannot_assign_selected_users(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck('id')->toArray();

        $response = $this->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-selected", [
            'user_ids' => $userIds,
        ]);

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_assign_selected_users(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('training.create'); // Has create permission but not assign_selected
        $training = Training::factory()->create();
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck('id')->toArray();

        $response = $this->actingAs($user)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-selected", [
            'user_ids' => $userIds,
        ]);

        $response->assertForbidden();
    }
}
