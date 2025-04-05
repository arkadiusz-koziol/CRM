<?php

namespace Tests\Feature\Admin\Material;

use App\Dto\MaterialDto;
use App\Models\Material;
use App\Models\User;
use App\Services\MaterialService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Mockery;

class MaterialUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_update_material(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.update');

        $material = Material::factory()->create([
            'name' => 'Steel',
            'description' => 'High-quality steel',
            'count' => 500,
            'price' => 150,
        ]);

        $payload = [
            'name' => 'Steel Updated',
            'description' => 'Updated high-quality steel',
            'count' => 450,
            'price' => 140,
        ];

        $mockService = Mockery::mock(MaterialService::class);
        $mockService->shouldReceive('updateMaterial')
            ->once()
            ->with(
                Mockery::on(fn($m) => $m->id === $material->id),
                Mockery::type(MaterialDto::class)
            )
            ->andReturnTrue();
        $this->app->instance(MaterialService::class, $mockService);

        $this->actingAs($admin)
            ->putJson("api/v1/admin/materials/{$material->id}", $payload)
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja zakończona sukcesem.']);
    }

    public function test_validation_error_when_data_missing(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.update');

        $material = Material::factory()->create();

        $this->actingAs($admin)
            ->putJson("api/v1/admin/materials/{$material->id}", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'count', 'price']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();
        $material = Material::factory()->create();

        $payload = [
            'name' => 'Steel Updated',
            'description' => 'Updated high-quality steel',
            'count' => 450,
            'price' => 140,
        ];

        $this->actingAs($user)
            ->putJson("api/v1/admin/materials/{$material->id}", $payload)
            ->assertForbidden();
    }

    public function test_failed_update_returns_bad_request(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.update');

        $material = Material::factory()->create([
            'name' => 'Steel',
            'description' => 'High-quality steel',
            'count' => 500,
            'price' => 150,
        ]);

        $payload = [
            'name' => 'Steel Updated',
            'description' => 'Updated high-quality steel',
            'count' => 450,
            'price' => 140,
        ];

        $mockService = Mockery::mock(MaterialService::class);
        $mockService->shouldReceive('updateMaterial')
            ->once()
            ->with(
                Mockery::on(fn($m) => $m->id === $material->id),
                Mockery::type(MaterialDto::class)
            )
            ->andReturnFalse();
        $this->app->instance(MaterialService::class, $mockService);

        $this->actingAs($admin)
            ->putJson("api/v1/admin/materials/{$material->id}", $payload)
            ->assertStatus(400)
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }
}
