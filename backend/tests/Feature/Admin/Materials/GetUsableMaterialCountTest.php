<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Materials;

use App\Models\Material;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetUsableMaterialCountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_get_usable_material_count(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.list');

        // Create materials with different counts
        Material::factory()->create(['count' => 100]);
        Material::factory()->create(['count' => 50]);
        Material::factory()->create(['count' => 0]); // Not usable
        Material::factory()->create(['count' => 25]);
        Material::factory()->create(['count' => null]); // Null count, not usable

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/admin/materials/usable-count');

        $response->assertOk()
            ->assertJson([
                'entity' => 'materials',
                'count' => 3, // Only materials with count > 0
            ]);
    }

    public function test_returns_zero_when_no_usable_materials(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.list');

        // Create materials with count 0 or null
        Material::factory()->create(['count' => 0]);
        Material::factory()->create(['count' => null]);

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/admin/materials/usable-count');

        $response->assertOk()
            ->assertJson([
                'entity' => 'materials',
                'count' => 0,
            ]);
    }

    public function test_unauthenticated_cannot_get_usable_material_count(): void
    {
        $this->getJson('/api/v1/admin/materials/usable-count')
            ->assertStatus(401);
    }

    public function test_user_without_permission_cannot_get_usable_material_count(): void
    {
        $user = User::factory()->create();
        // User has no permissions

        $this->actingAs($user)
            ->getJson('/api/v1/admin/materials/usable-count')
            ->assertStatus(403);
    }
}

