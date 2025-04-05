<?php

namespace Tests\Feature\Admin\Estate;

use App\Models\User;
use App\Services\EstateService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Mockery;

class EstateListTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_list_estates(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.list');

        $estatesData = [
            [
                'id' => 1,
                'name' => 'Biedronka',
                'custom_id' => 'AZL123',
                'street' => 'Gubaszewska',
                'postal_code' => '59-700',
                'house_number' => '12a/3',
                'city' => [
                    'id' => 11,
                    'name' => 'Wrocław',
                    'district' => 'Wrocław',
                    'commune' => 'Wrocław',
                    'voivodeship' => 'Dolnośląskie',
                ],
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 2,
                'name' => 'Lidl',
                'custom_id' => 'BLD456',
                'street' => 'Nowa',
                'postal_code' => '00-001',
                'house_number' => '5',
                'city' => [
                    'id' => 12,
                    'name' => 'Warszawa',
                    'district' => 'Warszawa',
                    'commune' => 'Warszawa',
                    'voivodeship' => 'Mazowieckie',
                ],
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];

        $mockService = Mockery::mock(EstateService::class);
        $mockService->shouldReceive('getAllEstates')
            ->once()
            ->andReturn($estatesData);
        $this->app->instance(EstateService::class, $mockService);

        $this->actingAs($admin)
            ->getJson('api/v1/admin/estates/list')
            ->assertOk()
            ->assertExactJson($estatesData);
    }

    public function test_service_failure_returns_error(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.list');

        $mockService = Mockery::mock(EstateService::class);
        $mockService->shouldReceive('getAllEstates')
            ->once()
            ->andThrow(new Exception('Coś poszło nie tak. Spróbuj ponownie później.'));
        $this->app->instance(EstateService::class, $mockService);

        $this->actingAs($admin)
            ->getJson('api/v1/admin/estates/list')
            ->assertStatus(500)
            ->assertJsonFragment([
                'message' => __('Coś poszło nie tak. Spróbuj ponownie później.'),
            ]);
    }

    public function test_unauthenticated_cannot_list_estates(): void
    {
        $this->getJson('api/v1/admin/estates/list')
            ->assertStatus(401);
    }
}
