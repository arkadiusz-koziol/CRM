<?php

namespace Tests\Feature\Admin\Material;

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MaterialStoreTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_material(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.create');

        $payload = [
            'name' => 'Steel',
            'description' => 'High-quality steel',
            'count' => 500,
            'price' => 150,
        ];

        $response = $this->actingAs($admin)
            ->postJson('api/v1/admin/materials', $payload)
            ->assertCreated();

        $response->assertJsonFragment($payload);

        $this->assertDatabaseHas('materials', $payload);
    }

    public function test_validation_error_when_data_missing(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('material.create');

        $this->actingAs($admin)
            ->postJson('api/v1/admin/materials', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'count', 'price']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'Steel',
            'description' => 'High-quality steel',
            'count' => 500,
            'price' => 150,
        ];

        $this->actingAs($user)
            ->postJson('api/v1/admin/materials', $payload)
            ->assertForbidden();
    }
}
