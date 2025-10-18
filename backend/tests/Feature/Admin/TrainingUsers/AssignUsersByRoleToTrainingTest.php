<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingUsers;

use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class AssignUsersByRoleToTrainingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->admin->givePermissionTo('training.user.assign_by_role');
    }

    public function test_admin_can_assign_users_by_role_to_training(): void
    {
        $training = Training::factory()->create();
        $role = Role::create(['name' => 'test_role']);

        $users = User::factory()->count(2)->create();
        foreach ($users as $user) {
            $user->assignRole($role);
        }

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-by-role", [
            'role' => 'test_role',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => __('app.training_user.users_by_role_assigned_successfully'),
                'data' => [
                    'training_id' => $training->id,
                    'role' => 'test_role',
                    'assigned_count' => 2,
                ],
            ]);

        foreach ($users as $user) {
            $this->assertDatabaseHas('training_user', [
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function test_admin_can_assign_users_by_role_to_training_with_no_users_in_role(): void
    {
        $training = Training::factory()->create();
        Role::create(['name' => 'empty_role']);

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-by-role", [
            'role' => 'empty_role',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => __('app.training_user.users_by_role_assigned_successfully'),
                'data' => [
                    'training_id' => $training->id,
                    'role' => 'empty_role',
                    'assigned_count' => 0,
                ],
            ]);
    }

    public function test_admin_cannot_assign_users_by_role_to_nonexistent_training(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/trainings/99999/users/assign-by-role', [
            'role' => 'test_role',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Training not found']);
    }

    public function test_assign_users_by_role_requires_role(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-by-role", []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_assign_users_by_role_handles_internal_server_error(): void
    {
        $training = Training::factory()->create();

        // Mock the service to throw an exception
        $this->mock(\App\Services\TrainingUserService::class, function ($mock) use ($training) {
            $mock->shouldReceive('assignUsersByRoleToTraining')
                ->once()
                ->with($training->id, 'test_role')
                ->andThrow(new \RuntimeException('Simulated internal error'));
        });

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-by-role", [
            'role' => 'test_role',
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }

    public function test_unauthenticated_user_cannot_assign_users_by_role(): void
    {
        $training = Training::factory()->create();

        $response = $this->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-by-role", [
            'role' => 'test_role',
        ]);

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_assign_users_by_role(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('training.create'); // Has create permission but not assign_by_role
        $training = Training::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/v1/admin/trainings/{$training->id}/users/assign-by-role", [
            'role' => 'test_role',
        ]);

        $response->assertForbidden();
    }
}
