<?php

namespace Tests\Feature\Admin\City;

use App\Models\City;
use App\Models\User;
use App\Services\CityService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CityDestroyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_delete_city(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.delete');

        $city = City::factory()->create();

        $mockService = Mockery::mock(CityService::class);
        $mockService->shouldReceive('deleteCity')
            ->once()
            ->with(Mockery::on(fn ($cityArg) => $cityArg->id === $city->id))
            ->andReturnUsing(function ($city) {
                $city->delete();

                return true;
            });
        $this->app->instance(CityService::class, $mockService);
        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/cities/{$city->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja zakończona sukcesem.']);
        $this->assertSoftDeleted('cities', ['id' => $city->id]);
    }

    public function test_failed_delete_returns_bad_request(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.delete');
        $city = City::factory()->create();
        $mockService = Mockery::mock(CityService::class);
        $mockService->shouldReceive('deleteCity')
            ->once()
            ->with(Mockery::on(fn ($cityArg) => $cityArg->id === $city->id))
            ->andReturnFalse();
        $this->app->instance(CityService::class, $mockService);

        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/cities/{$city->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();
        $city = City::factory()->create();

        $this->actingAs($user)
            ->deleteJson("api/v1/admin/cities/{$city->id}")
            ->assertForbidden();
    }
}
