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
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo('training.category.list');
    }

    public function test_admin_can_get_training_categories(): void
    {
        $categories = TrainingCategory::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/training-categories');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ]);

        // Check that our created categories are in the response
        $responseData = $response->json('data');
        $this->assertGreaterThanOrEqual(3, count($responseData));

        // Verify our specific categories are present
        $categoryNames = collect($responseData)->pluck('attributes.name')->toArray();
        foreach ($categories as $category) {
            $this->assertContains($category->name, $categoryNames);
        }
    }

    public function test_admin_gets_empty_array_if_no_categories(): void
    {
        // Clear any existing categories for this test
        TrainingCategory::query()->delete();

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/training-categories');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => []]);

        $responseData = $response->json('data');
        $this->assertIsArray($responseData);
    }

    public function test_unauthenticated_user_cannot_get_training_categories(): void
    {
        $response = $this->getJson('/api/v1/admin/training-categories');

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_get_training_categories(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/admin/training-categories');

        $response->assertForbidden();
    }
}
