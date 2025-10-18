<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class SimpleContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_simple_contract_creation(): void
    {
        // Seed permissions first
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        // Give permissions to user
        $user->givePermissionTo([
            'contract.view',
            'contract.create',
        ]);

        Sanctum::actingAs($user);

        // Create a contract
        $contract = Contract::factory()->create(['company_id' => $company->id]);

        // Test the API
        $response = $this->getJson('/api/v1/admin/contracts');

        if ($response->getStatusCode() !== 200) {
            echo 'Response Status: '.$response->getStatusCode().PHP_EOL;
            echo 'Response Content: '.$response->getContent().PHP_EOL;
        }

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
}
