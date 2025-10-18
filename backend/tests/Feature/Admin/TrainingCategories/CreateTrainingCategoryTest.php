<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingCategories;

use App\Models\TrainingCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class CreateTrainingCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo('training.category.create');
    }

    public function test_admin_can_create_training_category(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/training-categories', [
            'name' => 'Safety Training',
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJson([
                'data' => [
                    'type' => 'training-categories',
                    'attributes' => [
                        'name' => 'Safety Training',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('trainings_categories', [
            'name' => 'Safety Training',
        ]);
    }

    public function test_admin_cannot_create_duplicate_training_category(): void
    {
        TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/training-categories', [
            'name' => 'Safety Training',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_admin_cannot_create_training_category_without_name(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/training-categories', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_admin_cannot_create_training_category_with_empty_name(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/training-categories', [
            'name' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_admin_cannot_create_training_category_with_too_long_name(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/training-categories', [
            'name' => str_repeat('a', 256),
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_unauthenticated_user_cannot_create_training_category(): void
    {
        $response = $this->postJson('/api/v1/admin/training-categories', [
            'name' => 'Safety Training',
        ]);

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_create_training_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/admin/training-categories', [
            'name' => 'Safety Training',
        ]);

        $response->assertForbidden();
    }
}
