<?php

namespace Tests\Feature\Admin\Estate;

use App\Dto\EstateDto;
use App\Models\City;
use App\Models\Estate;
use App\Models\User;
use App\Services\EstateService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Mockery;

class EstateUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_update_estate(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.update');

        $estate = Estate::factory()->create([
            'name' => 'Biedronka',
            'custom_id' => 'AZL123',
            'street' => 'Gubaszewska',
            'postal_code' => '59-700',
            'house_number' => '12a/3',
        ]);

        $validCity = City::factory()->create();

        $payload = [
            'name' => 'Biedronka Updated',
            'custom_id' => 'AZL124',
            'street' => 'Updated Street',
            'postal_code' => '59-701',
            'city' => $validCity->id,
            'house_number' => '15b/2',
        ];

        $mockService = Mockery::mock(EstateService::class);
        $mockService->shouldReceive('updateEstate')
            ->once()
            ->with(
                Mockery::on(fn($estateArg) => $estateArg->id === $estate->id),
                Mockery::type(EstateDto::class)
            )
            ->andReturnTrue();
        $this->app->instance(EstateService::class, $mockService);

        $this->actingAs($admin)
            ->putJson("api/v1/admin/estates/{$estate->id}", $payload)
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja zakończona sukcesem.']);
    }


    public function test_validation_error_when_data_missing(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.update');

        $estate = Estate::factory()->create();

        $this->actingAs($admin)
            ->putJson("api/v1/admin/estates/{$estate->id}", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'custom_id', 'street', 'postal_code', 'city', 'house_number']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();
        $estate = Estate::factory()->create();

        $payload = [
            'name' => 'Biedronka Updated',
            'custom_id' => 'AZL124',
            'street' => 'Updated Street',
            'postal_code' => '59-701',
            'city' => City::factory()->create()->id,
            'house_number' => '15b/2',
        ];

        $this->actingAs($user)
            ->putJson("api/v1/admin/estates/{$estate->id}", $payload)
            ->assertForbidden();
    }

    public function test_failed_update_returns_bad_request(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.update');

        $estate = Estate::factory()->create([
            'name' => 'Biedronka',
            'custom_id' => 'AZL123',
            'street' => 'Gubaszewska',
            'postal_code' => '59-700',
            'house_number' => '12a/3',
        ]);

        $newCity = City::factory()->create();

        $payload = [
            'name' => 'Biedronka Updated',
            'custom_id' => 'AZL124',
            'street' => 'Updated Street',
            'postal_code' => '59-701',
            'city' => $newCity->id,
            'house_number' => '15b/2',
        ];

        $mockService = Mockery::mock(EstateService::class);
        $mockService->shouldReceive('updateEstate')
            ->once()
            ->with(
                Mockery::on(fn($estateArg) => $estateArg->id === $estate->id),
                Mockery::type(EstateDto::class)
            )
            ->andReturnFalse();
        $this->app->instance(EstateService::class, $mockService);

        $this->actingAs($admin)
            ->putJson("api/v1/admin/estates/{$estate->id}", $payload)
            ->assertStatus(400)
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }
}
