<?php

namespace Tests\Feature\Admin\City;

use App\Dto\CityDto;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CityStoreTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_city(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.create');

        $dto = new CityDto(
            name: 'Bolesławiec',
            district: 'Bolesławiec',
            commune: 'Bolesławiec',
            voivodeship: 'Dolnośląskie'
        );

        $this->actingAs($admin)
            ->postJson('api/v1/admin/cities', $dto->toArray())
            ->assertCreated()
            ->assertJsonFragment(['name' => 'Bolesławiec']);

        $this->assertDatabaseHas('cities', $dto->toArray());
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
