<?php

namespace Tests\Feature\Admin\Estate;

use App\Models\Estate;
use App\Models\User;
use App\Services\EstateService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class EstateDestroyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_delete_estate(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.delete');

        $estate = Estate::factory()->create();

        $mockService = Mockery::mock(EstateService::class);
        $mockService->shouldReceive('deleteEstate')
            ->once()
            ->with(Mockery::on(fn ($estateArg) => $estateArg->id === $estate->id))
            ->andReturnUsing(function ($estate) {
                $estate->delete();

                return true;
            });
        $this->app->instance(EstateService::class, $mockService);

        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/estates/{$estate->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja zakończona sukcesem.']);

        $this->assertSoftDeleted('estates', ['id' => $estate->id]);
    }

    public function test_failed_delete_returns_bad_request(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('estate.delete');

        $estate = Estate::factory()->create();

        $mockService = Mockery::mock(EstateService::class);
        $mockService->shouldReceive('deleteEstate')
            ->once()
            ->with(Mockery::on(fn ($estateArg) => $estateArg->id === $estate->id))
            ->andReturnFalse();
        $this->app->instance(EstateService::class, $mockService);

        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/estates/{$estate->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();
        $estate = Estate::factory()->create();

        $this->actingAs($user)
            ->deleteJson("api/v1/admin/estates/{$estate->id}")
            ->assertForbidden();
    }
}
