<?php

namespace Tests\Feature\Admin\Estate;

use App\Models\City;
use App\Models\Estate;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EstateStoreTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_estate(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.create');

        $city = City::factory()->create();

        $payload = [
            'name' => 'Biedronka',
            'custom_id' => 'AZL123',
            'street' => 'Gubaszewska',
            'postal_code' => '59-700',
            'city' => $city->id,
            'house_number' => '12a/3',
        ];

        $response = $this->actingAs($admin)
            ->postJson('api/v1/admin/estates', $payload)
            ->assertCreated();

        $response->assertJsonFragment([
            'name' => 'Biedronka',
            'custom_id' => 'AZL123',
            'street' => 'Gubaszewska',
            'postal_code' => '59-700',
            'house_number' => '12a/3',
        ]);

        $this->assertDatabaseHas('estates', [
            'name' => 'Biedronka',
            'custom_id' => 'AZL123',
            'street' => 'Gubaszewska',
            'postal_code' => '59-700',
            'city_id' => $city->id,
            'house_number' => '12a/3',
        ]);
    }

    public function test_validation_error_when_data_missing(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.create');

        $this->actingAs($admin)
            ->postJson('api/v1/admin/estates', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'street', 'postal_code', 'city', 'house_number']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();
        $city = City::factory()->create();
        $payload = [
            'name' => 'Biedronka',
            'custom_id' => 'AZL123',
            'street' => 'Gubaszewska',
            'postal_code' => '59-700',
            'city' => $city->id,
            'house_number' => '12a/3',
        ];

        $this->actingAs($user)
            ->postJson('api/v1/admin/estates', $payload)
            ->assertForbidden();
    }
}
