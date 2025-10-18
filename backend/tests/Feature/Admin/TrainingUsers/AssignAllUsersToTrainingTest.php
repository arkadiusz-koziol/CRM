<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingUsers;

use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class AssignAllUsersToTrainingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->admin->givePermissionTo('training.user.assign_all');
    }

    public function test_admin_can_assign_all_users_to_training(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-all");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => __('app.training_user.all_users_assigned_successfully'),
                'data' => [
                    'training_id' => $training->id,
                    'assigned_count' => 4, // 1 admin + 3 users created
                ],
            ]);

        foreach ($users as $user) {
            $this->assertDatabaseHas('training_user', [
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function test_admin_can_assign_all_users_to_training_with_no_users(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-all");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => __('app.training_user.all_users_assigned_successfully'),
                'data' => [
                    'training_id' => $training->id,
                    'assigned_count' => 1, // 1 admin user
                ],
            ]);
    }

    public function test_admin_cannot_assign_all_users_to_nonexistent_training(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/trainings/99999/users/assign-all');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Training not found']);
    }

    public function test_unauthenticated_user_cannot_assign_all_users_to_training(): void
    {
        $training = Training::factory()->create();

        $response = $this->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-all");

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_assign_all_users_to_training(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('training.create'); // Has create permission but not assign_all
        $training = Training::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-all");

        $response->assertForbidden();
    }

    public function test_assign_all_users_to_training_handles_internal_server_error(): void
    {
        $training = Training::factory()->create();

        // Mock the service to throw an exception
        $this->mock(\App\Services\TrainingUserService::class, function ($mock) use ($training) {
            $mock->shouldReceive('assignAllUsersToTraining')
                ->once()
                ->with($training->id)
                ->andThrow(new \RuntimeException('Simulated internal error'));
        });

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-all");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }
}
