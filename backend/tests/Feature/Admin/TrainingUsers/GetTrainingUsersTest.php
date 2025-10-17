<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingUsers;

use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class GetTrainingUsersTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->admin->givePermissionTo('training.user.list');
    }

    public function test_admin_can_get_training_users(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(2)->create();

        foreach ($users as $user) {
            TrainingUser::create([
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }

        $response = $this->actingAs($this->admin)->getJson("/v1/admin/trainings/{$training->id}/users");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'training_id',
                        'user_id',
                        'user' => [
                            'id',
                            'name',
                            'surname',
                            'email',
                        ],
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_admin_can_get_training_users_with_no_assignments(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->getJson("/v1/admin/trainings/{$training->id}/users");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['data' => []]);
    }

    public function test_admin_cannot_get_users_for_nonexistent_training(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/v1/admin/trainings/99999/users');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Training not found']);
    }

    public function test_unauthenticated_user_cannot_get_training_users(): void
    {
        $training = Training::factory()->create();

        $response = $this->getJson("/v1/admin/trainings/{$training->id}/users");

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_get_training_users(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $user->givePermissionTo('training.create'); // Has create permission but not list
        $training = Training::factory()->create();

        $response = $this->actingAs($user)->getJson("/v1/admin/trainings/{$training->id}/users");

        $response->assertForbidden();
    }

    public function test_get_training_users_handles_internal_server_error(): void
    {
        $training = Training::factory()->create();

        // Mock the service to throw an exception
        $this->mock(\App\Services\TrainingUserService::class, function ($mock) use ($training) {
            $mock->shouldReceive('getTrainingUsers')
                ->once()
                ->with($training->id)
                ->andThrow(new \RuntimeException('Simulated internal error'));
        });

        $response = $this->actingAs($this->admin)->getJson("/v1/admin/trainings/{$training->id}/users");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }
}
