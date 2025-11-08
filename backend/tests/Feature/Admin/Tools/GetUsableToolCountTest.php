<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Tools;

use App\Models\Tool;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetUsableToolCountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_get_usable_tool_count(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('tool.list');

        // Create tools with different counts
        Tool::factory()->create(['count' => 10]);
        Tool::factory()->create(['count' => 5]);
        Tool::factory()->create(['count' => 0]); // Not usable
        Tool::factory()->create(['count' => 3]);

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/admin/tools/usable-count');

        $response->assertOk()
            ->assertJson([
                'entity' => 'tools',
                'count' => 3, // Only tools with count > 0
            ]);
    }

    public function test_returns_zero_when_no_usable_tools(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('tool.list');

        // Create tools with count 0
        Tool::factory()->create(['count' => 0]);
        Tool::factory()->create(['count' => 0]);

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/admin/tools/usable-count');

        $response->assertOk()
            ->assertJson([
                'entity' => 'tools',
                'count' => 0,
            ]);
    }

    public function test_unauthenticated_cannot_get_usable_tool_count(): void
    {
        $this->getJson('/api/v1/admin/tools/usable-count')
            ->assertStatus(401);
    }

    public function test_user_without_permission_cannot_get_usable_tool_count(): void
    {
        $user = User::factory()->create();
        // User has no permissions

        $this->actingAs($user)
            ->getJson('/api/v1/admin/tools/usable-count')
            ->assertStatus(403);
    }
}

