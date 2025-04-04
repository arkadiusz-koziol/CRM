<?php

namespace Tests\Feature\Admin\City;

use App\Dto\CityDto;
use App\Models\City;
use App\Models\User;
use App\Services\CityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Mockery;

class CityUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_update_city(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.update');

        $city = City::factory()->create([
            'name' => 'Old City',
            'district' => 'Old District',
            'commune' => 'Old Commune',
            'voivodeship' => 'Old Voivodeship',
        ]);

        $payload = [
            'name' => 'New City',
            'district' => 'New District',
            'commune' => 'New Commune',
            'voivodeship' => 'New Voivodeship',
        ];

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cities/{$city->id}", $payload)
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja zakończona sukcesem.']);

        $this->assertDatabaseHas('cities', $payload);
    }

    public function test_validation_error_when_data_missing(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.update');
        $city = City::factory()->create();

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cities/{$city->id}", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'district', 'commune', 'voivodeship']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();
        $city = City::factory()->create();

        $payload = [
            'name' => 'New City',
            'district' => 'New District',
            'commune' => 'New Commune',
            'voivodeship' => 'New Voivodeship',
        ];

        $this->actingAs($user)
            ->putJson("api/v1/admin/cities/{$city->id}", $payload)
            ->assertForbidden();
    }

    public function test_failed_update_returns_bad_request(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.update');

        $city = City::factory()->create([
            'name' => 'Old City',
            'district' => 'Old District',
            'commune' => 'Old Commune',
            'voivodeship' => 'Old Voivodeship',
        ]);

        $payload = [
            'name' => 'New City',
            'district' => 'New District',
            'commune' => 'New Commune',
            'voivodeship' => 'New Voivodeship',
        ];

        $mockService = Mockery::mock(CityService::class);
        $mockService->shouldReceive('updateCity')
            ->once()
            ->with(
                Mockery::on(fn($cityArg) => $cityArg->id === $city->id),
                Mockery::type(CityDto::class)
            )
            ->andReturnFalse();

        $this->app->instance(CityService::class, $mockService);

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cities/{$city->id}", $payload)
            ->assertStatus(400)
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }

    public function test_update_non_existing_city_returns_not_found(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.update');

        $payload = [
            'name' => 'City',
            'district' => 'District',
            'commune' => 'Commune',
            'voivodeship' => 'Voivodeship',
        ];

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cities/99999999999", $payload)
            ->assertNotFound();
    }

}
