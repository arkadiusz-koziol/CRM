<?php

namespace Tests\Feature\Admin\Material;

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MaterialShowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_show_material(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.show');

        $material = Material::factory()->create();

        $this->actingAs($admin)
            ->getJson("api/v1/admin/materials/{$material->id}")
            ->assertOk()
            ->assertJson($material->toArray());
    }

    public function test_unauthenticated_cannot_show_material(): void
    {
        $material = Material::factory()->create();

        $this->getJson("api/v1/admin/materials/{$material->id}")
            ->assertStatus(401);
    }
}
