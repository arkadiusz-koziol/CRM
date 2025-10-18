<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Billing\InvoiceStatus;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class InvoiceApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private Company $company;

    private Contract $contract;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['created_by' => $this->user->id]);
        $this->contract = Contract::factory()->create(['company_id' => $this->company->id]);

        Sanctum::actingAs($this->user);

        $this->user->givePermissionTo([
            'invoice.view',
            'invoice.create',
            'invoice.update',
            'invoice.delete',
        ]);
    }

    public function test_it_can_list_invoices(): void
    {
        Invoice::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/v1/admin/invoices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes' => [
                            'number',
                            'company_id',
                            'contract_id',
                            'issue_date',
                            'due_date',
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

    public function test_it_can_filter_invoices_by_company(): void
    {
        $otherCompany = Company::factory()->create(['created_by' => $this->user->id]);

        Invoice::factory()->count(2)->create(['company_id' => $this->company->id]);
        Invoice::factory()->count(3)->create(['company_id' => $otherCompany->id]);

        $response = $this->getJson("/api/v1/admin/invoices?company_id={$this->company->id}");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_it_can_filter_invoices_by_contract(): void
    {
        $otherContract = Contract::factory()->create(['company_id' => $this->company->id]);

        Invoice::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'contract_id' => $this->contract->id,
        ]);
        Invoice::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'contract_id' => $otherContract->id,
        ]);

        $response = $this->getJson("/api/v1/admin/invoices?contract_id={$this->contract->id}");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_it_can_filter_invoices_by_status(): void
    {
        Invoice::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'status' => InvoiceStatus::PAID->value,
        ]);
        Invoice::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'status' => InvoiceStatus::ISSUED->value,
        ]);

        $response = $this->getJson('/api/v1/admin/invoices?status=paid');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_it_can_search_invoices(): void
    {
        Invoice::factory()->create([
            'company_id' => $this->company->id,
            'nr' => 'INV-001',
        ]);
        Invoice::factory()->create([
            'company_id' => $this->company->id,
            'nr' => 'INV-002',
        ]);

        $response = $this->getJson('/api/v1/admin/invoices?search=INV-001');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_it_can_create_invoice(): void
    {
        $data = [
            'number' => 'INV-001',
            'company_id' => $this->company->id,
            'contract_id' => $this->contract->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 5000.25,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ];

        $response = $this->postJson('/api/v1/admin/invoices', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'number',
                        'company_id',
                        'contract_id',
                        'issue_date',
                        'due_date',
                        'amount',
                        'currency',
                        'status',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('invoices', [
            'nr' => 'INV-001',
            'company_id' => $this->company->id,
            'contract_id' => $this->contract->id,
            'amount' => 5000.25,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ]);
    }

    public function test_it_can_create_invoice_without_contract(): void
    {
        $data = [
            'number' => 'INV-002',
            'company_id' => $this->company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 3000.00,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ];

        $response = $this->postJson('/api/v1/admin/invoices', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('invoices', [
            'nr' => 'INV-002',
            'company_id' => $this->company->id,
            'contract_id' => null,
        ]);
    }

    public function test_it_validates_invoice_creation(): void
    {
        $response = $this->postJson('/api/v1/admin/invoices', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'number',
                'company_id',
                'issue_date',
                'due_date',
                'amount',
                'currency',
                'status',
            ]);
    }

    public function test_it_validates_unique_invoice_number(): void
    {
        Invoice::factory()->create(['nr' => 'INV-001']);

        $data = [
            'number' => 'INV-001',
            'company_id' => $this->company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 5000.25,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ];

        $response = $this->postJson('/api/v1/admin/invoices', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['number']);
    }

    public function test_it_validates_date_relationships(): void
    {
        $data = [
            'number' => 'INV-001',
            'company_id' => $this->company->id,
            'issue_date' => '2024-01-31',
            'due_date' => '2024-01-01',
            'amount' => 5000.25,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ];

        $response = $this->postJson('/api/v1/admin/invoices', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['issue_date', 'due_date']);
    }

    public function test_it_can_show_invoice(): void
    {
        $invoice = Invoice::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/v1/admin/invoices/{$invoice->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'number',
                        'company_id',
                        'contract_id',
                        'issue_date',
                        'due_date',
                        'amount',
                        'currency',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_it_returns_404_for_nonexistent_invoice(): void
    {
        $response = $this->getJson('/api/v1/admin/invoices/nonexistent-id');

        $response->assertStatus(404);
    }

    public function test_it_can_update_invoice(): void
    {
        $invoice = Invoice::factory()->create(['company_id' => $this->company->id]);

        $data = [
            'amount' => 7500.50,
            'status' => InvoiceStatus::PAID->value,
        ];

        $response = $this->putJson("/api/v1/admin/invoices/{$invoice->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'amount' => 7500.50,
            'status' => InvoiceStatus::PAID->value,
        ]);
    }

    public function test_it_can_delete_invoice(): void
    {
        $invoice = Invoice::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/v1/admin/invoices/{$invoice->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
    }

    public function test_it_requires_authentication(): void
    {
        // Create a fresh application instance without authentication
        $this->refreshApplication();

        $response = $this->getJson('/api/v1/admin/invoices');

        $response->assertStatus(401);
    }

    public function test_it_requires_permissions(): void
    {
        $this->user->revokePermissionTo('invoice.view');

        $response = $this->getJson('/api/v1/admin/invoices');

        $response->assertStatus(403);
    }
}
