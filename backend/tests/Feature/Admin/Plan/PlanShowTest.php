<?php

namespace Tests\Feature\Admin\Plan;

use App\Models\Estate;
use App\Models\Plan;
use App\Models\User;
use App\Services\PlanService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Mockery;
use Spatie\Permission\Models\Permission;

class PlanShowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'plan.list']);
    }

    public function test_admin_can_show_plan(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('plan.list');

        $estate = Estate::factory()->create();
        $plan = Plan::factory()->create(['estate_id' => $estate->id]);

        $mockService = Mockery::mock(PlanService::class);
        $mockService->shouldReceive('getPlansByEstate')
            ->once()
            ->with(Mockery::on(fn($estateArg) => $estateArg->id === $estate->id))
            ->andReturn(collect($plan));
        $this->app->instance(PlanService::class, $mockService);

        $this->actingAs($admin)
            ->getJson("api/v1/admin/plans/{$estate->id}")
            ->assertOk()
            ->assertJson($plan->toArray());
    }

    public function test_plan_show_not_found_returns_404(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('plan.list');

        $estate = Estate::factory()->create();

        $mockService = Mockery::mock(PlanService::class);
        $mockService->shouldReceive('getPlansByEstate')
            ->once()
            ->with(Mockery::on(fn($estateArg) => $estateArg->id === $estate->id))
            ->andThrow(new ModelNotFoundException());
        $this->app->instance(PlanService::class, $mockService);

        $this->actingAs($admin)
            ->getJson("api/v1/admin/plans/{$estate->id}")
            ->assertStatus(404)
            ->assertJsonFragment(['message' => 'Nie znaleziono planu dla tej nieruchomości']);
    }

    public function test_unauthenticated_cannot_show_plan(): void
    {
        $estate = Estate::factory()->create();

        $this->getJson("api/v1/admin/plans/{$estate->id}")
            ->assertStatus(401);
    }
}
