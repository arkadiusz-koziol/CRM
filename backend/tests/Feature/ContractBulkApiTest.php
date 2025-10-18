<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Billing\ContractStatus;
use App\Models\Company;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ContractBulkApiTest extends TestCase
{
    use RefreshDatabase;

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
            'contract.update',
        ]);
    }

    public function test_it_can_bulk_update_contract_status(): void
    {
        $contracts = Contract::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'status' => ContractStatus::DRAFT->value,
        ]);

        $data = [
            'ids' => $contracts->pluck('id')->toArray(),
            'status' => ContractStatus::ACTIVE->value,
        ];

        $response = $this->postJson('/api/v1/admin/contracts/bulk/update-status', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully updated 3 contracts',
                'updated_count' => 3,
            ]);

        foreach ($contracts as $contract) {
            $this->assertDatabaseHas('contracts', [
                'id' => $contract->id,
                'status' => ContractStatus::ACTIVE->value,
            ]);
        }
    }

    public function test_it_validates_bulk_update_request(): void
    {
        $response = $this->postJson('/api/v1/admin/contracts/bulk/update-status', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids', 'status']);
    }

    public function test_it_validates_contract_ids_exist(): void
    {
        $data = [
            'ids' => ['nonexistent-id-1', 'nonexistent-id-2'],
            'status' => ContractStatus::ACTIVE->value,
        ];

        $response = $this->postJson('/api/v1/admin/contracts/bulk/update-status', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids.0', 'ids.1']);
    }

    public function test_it_validates_status_enum(): void
    {
        $contract = Contract::factory()->create(['company_id' => $this->company->id]);

        $data = [
            'ids' => [$contract->id],
            'status' => 'invalid-status',
        ];

        $response = $this->postJson('/api/v1/admin/contracts/bulk/update-status', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_it_requires_authentication(): void
    {
        // Create a fresh application instance without authentication
        $this->refreshApplication();

        $data = [
            'ids' => ['some-id'],
            'status' => ContractStatus::ACTIVE->value,
        ];

        $response = $this->postJson('/api/v1/admin/contracts/bulk/update-status', $data);

        $response->assertStatus(401);
    }

    public function test_it_requires_permissions(): void
    {
        $this->user->revokePermissionTo('contract.update');

        $data = [
            'ids' => ['some-id'],
            'status' => ContractStatus::ACTIVE->value,
        ];

        $response = $this->postJson('/api/v1/admin/contracts/bulk/update-status', $data);

        $response->assertStatus(403);
    }
}
