<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingUsers;

use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class RemoveUserFromTrainingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->admin->givePermissionTo('training.user.remove');
    }

    public function test_admin_can_remove_user_from_training(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        // First assign the user
        TrainingUser::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/{$training->id}/users/{$user->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('training_user', [
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_cannot_remove_user_not_assigned_to_training(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/{$training->id}/users/{$user->id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => __('app.training_user.not_assigned')]);
    }

    public function test_admin_cannot_remove_user_from_nonexistent_training(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/99999/users/{$user->id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Training not found']);
    }

    public function test_admin_cannot_remove_nonexistent_user_from_training(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/{$training->id}/users/99999");

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'User not found']);
    }

    public function test_unauthenticated_user_cannot_remove_user_from_training(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $response = $this->deleteJson("/v1/admin/trainings/{$training->id}/users/{$user->id}");

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_remove_user_from_training(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $user->givePermissionTo('training.create'); // Has create permission but not remove
        $training = Training::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/v1/admin/trainings/{$training->id}/users/{$targetUser->id}");

        $response->assertForbidden();
    }

    public function test_remove_user_from_training_handles_internal_server_error(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        // Mock the service to throw an exception
        $this->mock(\App\Services\TrainingUserService::class, function ($mock) use ($training, $user) {
            $mock->shouldReceive('removeUserFromTraining')
                ->once()
                ->with($training->id, $user->id)
                ->andThrow(new \RuntimeException('Simulated internal error'));
        });

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/{$training->id}/users/{$user->id}");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }
}
