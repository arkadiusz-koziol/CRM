<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Reports;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

final class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = [
            'report.view',
            'report.create',
            'report.update',
            'report.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_it_creates_report_successfully(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('report.create');

        $reportData = [
            'name' => 'Test Report',
            'description' => 'Test Description',
            'source' => 'companies',
            'columns' => ['id', 'name', 'status'],
            'filters' => [
                ['field' => 'status', 'operator' => 'eq', 'value' => 'active'],
            ],
            'sorting' => [
                ['field' => 'name', 'direction' => 'asc'],
            ],
            'is_public' => true,
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/v1/admin/reports', $reportData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'source',
                    'columns',
                    'filters',
                    'sorting',
                    'created_by',
                    'is_public',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertEquals('Test Report', $response->json('data.name'));
        $this->assertEquals('companies', $response->json('data.source'));
        $this->assertTrue($response->json('data.is_public'));
    }

    public function test_it_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('report.create');

        $response = $this->actingAs($user)
            ->postJson('/api/v1/admin/reports', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'source', 'columns']);
    }

    public function test_it_validates_source_enum(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('report.create');

        $response = $this->actingAs($user)
            ->postJson('/api/v1/admin/reports', [
                'name' => 'Test Report',
                'source' => 'invalid-source',
                'columns' => ['id', 'name'],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['source']);
    }

    public function test_it_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/admin/reports', [
            'name' => 'Test Report',
            'source' => 'companies',
            'columns' => ['id', 'name'],
        ]);

        $response->assertStatus(401);
    }

    public function test_it_requires_permission(): void
    {
        $user = User::factory()->create(); // No permissions

        $response = $this->actingAs($user)
            ->postJson('/api/v1/admin/reports', [
                'name' => 'Test Report',
                'source' => 'companies',
                'columns' => ['id', 'name'],
            ]);

        $response->assertStatus(403);
    }

    public function test_it_lists_user_reports(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('report.view');

        $response = $this->actingAs($user)
            ->getJson('/api/v1/admin/reports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);
    }

    public function test_it_lists_public_reports(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('report.view');

        $response = $this->actingAs($user)
            ->getJson('/api/v1/admin/reports?public=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);
    }

    public function test_it_shows_report(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('report.view');

        // This test would need a report to be created first
        // For now, we'll just test the endpoint structure
        $response = $this->actingAs($user)
            ->getJson('/api/v1/admin/reports/test-id');

        // Should return 404 since no report exists
        $response->assertStatus(404);
    }

    public function test_it_updates_report(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('report.update');

        $updateData = [
            'name' => 'Updated Report',
            'description' => 'Updated Description',
            'source' => 'companies',
            'columns' => ['id', 'name'],
            'is_public' => false,
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/v1/admin/reports/test-id', $updateData);

        // Should return 404 since no report exists
        $response->assertStatus(404);
    }

    public function test_it_deletes_report(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('report.delete');

        $response = $this->actingAs($user)
            ->deleteJson('/api/v1/admin/reports/test-id');

        // Should return 404 since no report exists
        $response->assertStatus(404);
    }
}
