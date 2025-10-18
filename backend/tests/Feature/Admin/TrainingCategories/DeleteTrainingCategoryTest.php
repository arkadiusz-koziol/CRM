<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingCategories;

use App\Models\TrainingCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class DeleteTrainingCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo('training.category.delete');
    }

    public function test_admin_can_delete_training_category(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->actingAs($this->admin)->deleteJson("/api/v1/admin/training-categories/{$trainingCategory->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => __('app.category.deleted_successfully')]);

        $this->assertSoftDeleted('trainings_categories', [
            'id' => $trainingCategory->id,
        ]);
    }

    public function test_admin_cannot_delete_non_existent_training_category(): void
    {
        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/admin/training-categories/non-existent-uuid');

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }

    public function test_unauthenticated_user_cannot_delete_training_category(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->deleteJson("/api/v1/admin/training-categories/{$trainingCategory->id}");

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_delete_training_category(): void
    {
        $user = User::factory()->create();
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $response = $this->actingAs($user)->deleteJson("/api/v1/admin/training-categories/{$trainingCategory->id}");

        $response->assertForbidden();
    }
}
