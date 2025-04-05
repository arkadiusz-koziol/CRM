<?php

namespace Tests\Feature\Admin\Material;

use App\Models\Material;
use App\Models\User;
use App\Services\MaterialService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Mockery;

class MaterialDestroyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_delete_material(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.delete');

        $material = Material::factory()->create();

        $mockService = Mockery::mock(MaterialService::class);
        $mockService->shouldReceive('deleteMaterial')
            ->once()
            ->with(Mockery::on(fn($m) => $m->id === $material->id))
            ->andReturnUsing(function ($material) {
                $material->delete();
                return true;
            });
        $this->app->instance(MaterialService::class, $mockService);

        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/materials/{$material->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja zakończona sukcesem.']);

        $this->assertSoftDeleted('materials', ['id' => $material->id]);
    }

    public function test_failed_delete_returns_bad_request(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.delete');

        $material = Material::factory()->create();

        $mockService = Mockery::mock(MaterialService::class);
        $mockService->shouldReceive('deleteMaterial')
            ->once()
            ->with(Mockery::on(fn($m) => $m->id === $material->id))
            ->andReturnFalse();
        $this->app->instance(MaterialService::class, $mockService);

        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/materials/{$material->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();
        $material = Material::factory()->create();

        $this->actingAs($user)
            ->deleteJson("api/v1/admin/materials/{$material->id}")
            ->assertForbidden();
    }
}
