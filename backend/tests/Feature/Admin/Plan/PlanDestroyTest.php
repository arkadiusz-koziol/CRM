<?php

namespace Tests\Feature\Admin\Plan;

use App\Models\Estate;
use App\Models\Plan;
use App\Models\User;
use App\Services\PlanService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Mockery;
use Spatie\Permission\Models\Permission;

class PlanDestroyTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'plan.delete']);
    }

    public function test_admin_can_delete_plan(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('plan.delete');

        $estate = Estate::factory()->create();
        $plan = Plan::factory()->create(['estate_id' => $estate->id]);

        $mockService = Mockery::mock(PlanService::class);
        $mockService->shouldReceive('getPlansByEstate')
            ->once()
            ->with(Mockery::on(fn($estateArg) => $estateArg->id === $estate->id))
            ->andReturn(collect($plan));
        $mockService->shouldReceive('deletePlan')
            ->once()
            ->with(Mockery::type(Collection::class))
            ->andReturnTrue();
        $this->app->instance(PlanService::class, $mockService);

        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/plans/{$estate->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Akcja zakończona sukcesem.']);
    }

    public function test_plan_not_found_returns_404(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('plan.delete');

        $estate = Estate::factory()->create();

        $mockService = Mockery::mock(PlanService::class);
        $mockService->shouldReceive('getPlansByEstate')
            ->once()
            ->with(Mockery::on(fn($estateArg) => $estateArg->id === $estate->id))
            ->andThrow(new ModelNotFoundException());
        $this->app->instance(PlanService::class, $mockService);

        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/plans/{$estate->id}")
            ->assertStatus(404)
            ->assertJsonFragment(['message' => 'Nie znaleziono planu dla tej nieruchomości']);
    }

    public function test_exception_during_delete_returns_500(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('plan.delete');

        $estate = Estate::factory()->create();

        $mockService = Mockery::mock(PlanService::class);
        $mockService->shouldReceive('getPlansByEstate')
            ->once()
            ->with(Mockery::on(fn($estateArg) => $estateArg->id === $estate->id))
            ->andThrow(new Exception('Delete error'));
        $this->app->instance(PlanService::class, $mockService);

        $this->actingAs($admin)
            ->deleteJson("api/v1/admin/plans/{$estate->id}")
            ->assertStatus(500)
            ->assertJsonFragment(['message' => 'Błąd podczas usuwania planów']);
    }

    public function test_forbidden_without_permission(): void
    {
        $user = User::factory()->create();
        $estate = Estate::factory()->create();

        $this->actingAs($user)
            ->deleteJson("api/v1/admin/plans/{$estate->id}")
            ->assertForbidden();
    }
}
