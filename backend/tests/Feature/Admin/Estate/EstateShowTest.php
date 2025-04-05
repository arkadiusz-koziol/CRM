<?php

namespace Tests\Feature\Admin\Estate;

use App\Models\Estate;
use App\Models\User;
use App\Services\EstateService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Mockery;

class EstateShowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_show_estate(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.show');

        $estate = Estate::factory()->create();

        $mockService = Mockery::mock(EstateService::class);
        $mockService->shouldReceive('findEstateById')
            ->once()
            ->with($estate->id)
            ->andReturn($estate);
        $this->app->instance(EstateService::class, $mockService);

        $this->actingAs($admin)
            ->getJson("api/v1/admin/estates/{$estate->id}")
            ->assertOk()
            ->assertJson($estate->toArray());
    }

    public function test_unauthenticated_cannot_show_estate(): void
    {
        $estate = Estate::factory()->create();
        $this->getJson("api/v1/admin/estates/{$estate->id}")
            ->assertStatus(401);
    }
}
