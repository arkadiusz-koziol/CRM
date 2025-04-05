<?php

namespace Tests\Feature\Admin\Material;

use App\Models\User;
use App\Services\MaterialService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Tests\TestCase;
use Mockery;

class MaterialListTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_list_materials(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.list');

        $materialsData = [
            [
                'id' => 1,
                'name' => 'Nail',
                'description' => 'Big head, made from steel, wood-use.',
                'count' => 1000000,
                'price' => 10000,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Screw',
                'description' => 'High-quality stainless steel screw.',
                'count' => 500000,
                'price' => 5000,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'deleted_at' => null,
            ],
        ];

        $mockService = Mockery::mock(MaterialService::class);
        $mockService->shouldReceive('getAllMaterials')
            ->once()
            ->andReturn($materialsData);
        $this->app->instance(MaterialService::class, $mockService);

        $this->actingAs($admin)
            ->getJson('api/v1/admin/materials/list')
            ->assertOk()
            ->assertExactJson($materialsData);
    }

    public function test_service_failure_returns_error(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.list');

        $mockService = Mockery::mock(MaterialService::class);
        $mockService->shouldReceive('getAllMaterials')
            ->once()
            ->andThrow(new Exception('Coś poszło nie tak. Spróbuj ponownie później.'));
        $this->app->instance(MaterialService::class, $mockService);

        $this->actingAs($admin)
            ->getJson('api/v1/admin/materials/list')
            ->assertStatus(500)
            ->assertJsonFragment([
                'message' => __('Coś poszło nie tak. Spróbuj ponownie później.'),
            ]);
    }

    public function test_unauthenticated_cannot_list_materials(): void
    {
        $this->getJson('api/v1/admin/materials/list')
            ->assertStatus(401);
    }
}
