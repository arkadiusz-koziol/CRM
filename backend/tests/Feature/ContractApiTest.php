<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Billing\ContractStatus;
use App\Models\Company;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ContractApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['created_by' => $this->user->id]);

        Sanctum::actingAs($this->user);

        $this->user->givePermissionTo([
            'contract.view',
            'contract.create',
            'contract.update',
            'contract.delete',
        ]);
    }

    public function test_it_can_list_contracts(): void
    {
        Contract::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/v1/admin/contracts');

        if ($response->getStatusCode() !== 200) {
            echo 'Response Status: '.$response->getStatusCode().PHP_EOL;
            echo 'Response Content: '.$response->getContent().PHP_EOL;
        }

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes' => [
                            'number',
                            'company_id',
                            'start_at',
                            'end_at',
                            'amount',
                            'currency',
                            'status',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'meta' => [
                    'total',
                ],
            ]);
    }

    public function test_it_can_filter_contracts_by_company(): void
    {
        $otherCompany = Company::factory()->create(['created_by' => $this->user->id]);

        Contract::factory()->count(2)->create(['company_id' => $this->company->id]);
        Contract::factory()->count(3)->create(['company_id' => $otherCompany->id]);

        $response = $this->getJson("/api/v1/admin/contracts?company_id={$this->company->id}");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_it_can_filter_contracts_by_status(): void
    {
        Contract::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'status' => ContractStatus::ACTIVE->value,
        ]);
        Contract::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'status' => ContractStatus::DRAFT->value,
        ]);

        $response = $this->getJson('/api/v1/admin/contracts?status=active');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_it_can_search_contracts(): void
    {
        Contract::factory()->create([
            'company_id' => $this->company->id,
            'nr' => 'CON-001',
        ]);
        Contract::factory()->create([
            'company_id' => $this->company->id,
            'nr' => 'CON-002',
        ]);

        $response = $this->getJson('/api/v1/admin/contracts?search=CON-001');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_it_can_create_contract(): void
    {
        $data = [
            'number' => 'CON-001',
            'company_id' => $this->company->id,
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 10000.50,
            'currency' => 'USD',
            'status' => ContractStatus::DRAFT->value,
        ];

        $response = $this->postJson('/api/v1/admin/contracts', $data);

        if ($response->getStatusCode() !== 201) {
            echo 'Response Status: '.$response->getStatusCode().PHP_EOL;
            echo 'Response Content: '.$response->getContent().PHP_EOL;
        }

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'number',
                        'company_id',
                        'start_at',
                        'end_at',
                        'amount',
                        'currency',
                        'status',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('contracts', [
            'nr' => 'CON-001',
            'company_id' => $this->company->id,
            'amount' => 10000.50,
            'currency' => 'USD',
            'status' => ContractStatus::DRAFT->value,
        ]);
    }

    public function test_it_validates_contract_creation(): void
    {
        $response = $this->postJson('/api/v1/admin/contracts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'number',
                'company_id',
                'start_at',
                'end_at',
                'amount',
                'currency',
                'status',
            ]);
    }

    public function test_it_validates_unique_contract_number(): void
    {
        Contract::factory()->create(['nr' => 'CON-001']);

        $data = [
            'number' => 'CON-001',
            'company_id' => $this->company->id,
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 10000.50,
            'currency' => 'USD',
            'status' => ContractStatus::DRAFT->value,
        ];

        $response = $this->postJson('/api/v1/admin/contracts', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['number']);
    }

    public function test_it_validates_date_relationships(): void
    {
        $data = [
            'number' => 'CON-001',
            'company_id' => $this->company->id,
            'start_at' => '2024-12-31',
            'end_at' => '2024-01-01',
            'amount' => 10000.50,
            'currency' => 'USD',
            'status' => ContractStatus::DRAFT->value,
        ];

        $response = $this->postJson('/api/v1/admin/contracts', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_at', 'end_at']);
    }

    public function test_it_can_show_contract(): void
    {
        $contract = Contract::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/v1/admin/contracts/{$contract->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'number',
                        'company_id',
                        'start_at',
                        'end_at',
                        'amount',
                        'currency',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_it_returns_404_for_nonexistent_contract(): void
    {
        $response = $this->getJson('/api/v1/admin/contracts/nonexistent-id');

        if ($response->getStatusCode() !== 404) {
            echo 'Response Status: '.$response->getStatusCode().PHP_EOL;
            echo 'Response Content: '.$response->getContent().PHP_EOL;
        }

        $response->assertStatus(404);
    }

    public function test_it_can_update_contract(): void
    {
        $contract = Contract::factory()->create(['company_id' => $this->company->id]);

        $data = [
            'amount' => 15000.75,
            'status' => ContractStatus::ACTIVE->value,
        ];

        $response = $this->putJson("/api/v1/admin/contracts/{$contract->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'amount' => 15000.75,
            'status' => ContractStatus::ACTIVE->value,
        ]);
    }

    public function test_it_can_delete_contract(): void
    {
        $contract = Contract::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/v1/admin/contracts/{$contract->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('contracts', ['id' => $contract->id]);
    }

    public function test_it_requires_authentication(): void
    {
        // Create a fresh application instance without authentication
        $this->refreshApplication();

        $response = $this->getJson('/api/v1/admin/contracts');

        $response->assertStatus(401);
    }

    public function test_it_requires_permissions(): void
    {
        $this->user->revokePermissionTo('contract.view');

        $response = $this->getJson('/api/v1/admin/contracts');

        $response->assertStatus(403);
    }
}
