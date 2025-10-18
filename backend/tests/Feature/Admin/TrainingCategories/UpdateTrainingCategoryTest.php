<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingCategories;

use App\Models\TrainingCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class UpdateTrainingCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->admin->givePermissionTo('training.category.update');
    }

    public function test_admin_can_update_training_category(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->actingAs($this->admin)->putJson("/v1/admin/training-categories/{$trainingCategory->id}", [
            'name' => 'Updated Safety Training',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Updated Safety Training',
                ],
            ]);

        $this->assertDatabaseHas('trainings_categories', [
            'id' => $trainingCategory->id,
            'name' => 'Updated Safety Training',
        ]);
    }

    public function test_admin_cannot_update_training_category_to_duplicate_name(): void
    {
        TrainingCategory::factory()->create(['name' => 'Existing Training']);
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->actingAs($this->admin)->putJson("/v1/admin/training-categories/{$trainingCategory->id}", [
            'name' => 'Existing Training',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_admin_can_update_training_category_to_same_name(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->actingAs($this->admin)->putJson("/v1/admin/training-categories/{$trainingCategory->id}", [
            'name' => 'Safety Training',
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_admin_cannot_update_non_existent_training_category(): void
    {
        $response = $this->actingAs($this->admin)->putJson('/v1/admin/training-categories/non-existent-uuid', [
            'name' => 'Updated Safety Training',
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }

    public function test_admin_cannot_update_training_category_without_name(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->actingAs($this->admin)->putJson("/v1/admin/training-categories/{$trainingCategory->id}", []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_unauthenticated_user_cannot_update_training_category(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->putJson("/v1/admin/training-categories/{$trainingCategory->id}", [
            'name' => 'Updated Safety Training',
        ]);

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_update_training_category(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->actingAs($user)->putJson("/v1/admin/training-categories/{$trainingCategory->id}", [
            'name' => 'Updated Safety Training',
        ]);

        $response->assertForbidden();
    }
}
