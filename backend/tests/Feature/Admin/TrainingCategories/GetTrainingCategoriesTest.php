<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingCategories;

use App\Models\TrainingCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class GetTrainingCategoriesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->admin->givePermissionTo('training.category.list');
    }

    public function test_admin_can_get_training_categories(): void
    {
        TrainingCategory::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->getJson('/v1/admin/training-categories');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_admin_gets_empty_array_if_no_categories(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/v1/admin/training-categories');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(0, 'data');
    }

    public function test_unauthenticated_user_cannot_get_training_categories(): void
    {
        $response = $this->getJson('/v1/admin/training-categories');

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_get_training_categories(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->getJson('/v1/admin/training-categories');

        $response->assertForbidden();
    }
}
