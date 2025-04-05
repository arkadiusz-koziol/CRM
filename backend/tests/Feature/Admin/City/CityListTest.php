<?php

namespace Tests\Feature\Admin\City;

use App\Models\User;
use App\Services\CityService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Tests\TestCase;
use Mockery;

class CityListTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_list_cities(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.list');

        $citiesData = [
            [
                'id' => 1,
                'name' => 'Wrocław',
                'district' => 'Wrocław',
                'commune' => 'Wrocław',
                'voivodeship' => 'Dolnośląskie',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 2,
                'name' => 'Warszawa',
                'district' => 'Warszawa',
                'commune' => 'Warszawa',
                'voivodeship' => 'Mazowieckie',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];

        $mockService = Mockery::mock(CityService::class);
        $mockService->shouldReceive('getAllCities')
            ->once()
            ->andReturn($citiesData);
        $this->app->instance(CityService::class, $mockService);

        $this->actingAs($admin)
            ->getJson('api/v1/admin/cities/list')
            ->assertOk()
            ->assertExactJson($citiesData);
    }

    public function test_service_failure_returns_unprocessable_entity(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('city.list');

        $exceptionMessage = 'Test exception';
        $mockService = Mockery::mock(CityService::class);
        $mockService->shouldReceive('getAllCities')
            ->once()
            ->andThrow(new Exception($exceptionMessage));
        $this->app->instance(CityService::class, $mockService);

        $this->actingAs($admin)
            ->getJson('api/v1/admin/cities/list')
            ->assertStatus(SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([$exceptionMessage]);
    }

    public function test_unauthenticated_cannot_list_cities(): void
    {
        $this->getJson('api/v1/admin/cities/list')
            ->assertStatus(401);
    }
}
