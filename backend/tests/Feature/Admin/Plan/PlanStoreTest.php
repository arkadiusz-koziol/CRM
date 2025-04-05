<?php

namespace Tests\Feature\Admin\Plan;

use App\Models\Estate;
use App\Models\Plan;
use App\Models\User;
use App\Services\PlanService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Mockery;
use Spatie\Permission\Models\Permission;

class PlanStoreTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'plan.create']);
    }

    public function test_admin_can_create_plan(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('plan.create');

        $estate = Estate::factory()->create();
        $file = UploadedFile::fake()->create('plan.pdf', 100, 'application/pdf');

        $planData = [
            'estate_id'  => $estate->id,
            'file_path'  => 'plans/pdf/plan.pdf',
            'image_path' => 'plans/images/plan.jpg',
        ];
        $plan = Plan::factory()->make($planData);

        $mockService = Mockery::mock(PlanService::class);
        $mockService->shouldReceive('createPlan')
            ->once()
            ->with(
                Mockery::any(),
                Mockery::on(fn($estateArg) => $estateArg->id === $estate->id)
            )
            ->andReturn($plan);
        $this->app->instance(PlanService::class, $mockService);

        $this->actingAs($admin)
            ->post("api/v1/admin/plans/{$estate->id}", ['file' => $file])
            ->assertStatus(201)
            ->assertJson($plan->toArray());
    }
}
