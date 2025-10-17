<?php

namespace Tests\Feature\Admin\City;

use App\Dto\CityDto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\PermissionSeeder;

class CityStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_create_city(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.create');

        $cityData = [
            'name' => 'TestCity' . uniqid(),
            'district' => 'TestDistrict',
            'commune' => 'TestCommune',
            'voivodeship' => 'TestVoivodeship'
        ];

        $this->actingAs($admin)
            ->postJson('api/v1/admin/cities', $cityData)
            ->assertCreated()
            ->assertJsonFragment(['name' => $cityData['name']]);

        $this->assertDatabaseHas('cities', $cityData);
    }

    public function test_validation_error_when_data_missing(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.create');

        $this->actingAs($admin)
            ->postJson('api/v1/admin/cities', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'district', 'commune', 'voivodeship']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();

        $dto = new CityDto(
            name: 'Bolesławiec',
            district: 'Bolesławiec',
            commune: 'Bolesławiec',
            voivodeship: 'Dolnośląskie'
        );

        $this->actingAs($user)
            ->postJson('api/v1/admin/cities', $dto->toArray())
            ->assertForbidden();
    }
}
