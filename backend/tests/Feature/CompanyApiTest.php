<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user->givePermissionTo([
            'company.view',
            'company.create',
            'company.update',
            'company.delete',
        ]);
    }

    public function test_can_list_companies(): void
    {
        Company::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/admin/companies');

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'industry',
                            'source',
                            'status',
                            'region',
                            'vat_id',
                            'created_by',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ],
            ]);
    }

    public function test_can_create_company(): void
    {
        $companyData = [
            'name' => 'Test Company',
            'industry' => 'Technology',
            'source' => 'website',
            'status' => 'prospect',
            'region' => 'North America',
            'vat_id' => 'US123456789',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/admin/companies', $companyData);

        if ($response->status() !== 201) {
            dump($response->json());
        }

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'industry',
                        'source',
                        'status',
                        'region',
                        'vat_id',
                        'created_by',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'industry' => 'Technology',
            'source' => 'website',
            'status' => 'prospect',
            'region' => 'North America',
            'vat_id' => 'US123456789',
        ]);
    }

    public function test_can_show_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/admin/companies/{$company->id}");

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'industry',
                        'source',
                        'status',
                        'region',
                        'vat_id',
                        'created_by',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_can_update_company(): void
    {
        $company = Company::factory()->create();

        $updateData = [
            'name' => 'Updated Company Name',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/admin/companies/{$company->id}", $updateData);

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company Name',
            'status' => 'active',
        ]);
    }

    public function test_can_delete_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/admin/companies/{$company->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_can_filter_companies(): void
    {
        Company::factory()->create(['name' => 'Tech Corp', 'status' => 'active']);
        Company::factory()->create(['name' => 'Business Inc', 'status' => 'prospect']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/admin/companies?name=Tech&status=active');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Tech Corp', $data[0]['attributes']['name']);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/admin/companies');
        $response->assertStatus(401);
    }

    public function test_requires_permission(): void
    {
        $userWithoutPermission = User::factory()->create();

        $response = $this->actingAs($userWithoutPermission)
            ->getJson('/api/v1/admin/companies');

        $response->assertStatus(403);
    }
}
