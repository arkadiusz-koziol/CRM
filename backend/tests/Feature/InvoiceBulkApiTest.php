<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Billing\InvoiceStatus;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class InvoiceBulkApiTest extends TestCase
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
            'invoice.view',
            'invoice.update',
        ]);
    }

    public function test_it_can_bulk_update_invoice_status(): void
    {
        $invoices = Invoice::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'status' => InvoiceStatus::ISSUED->value,
        ]);

        $data = [
            'ids' => $invoices->pluck('id')->toArray(),
            'status' => InvoiceStatus::PAID->value,
        ];

        $response = $this->postJson('/api/v1/admin/invoices/bulk/update-status', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully updated 3 invoices',
                'updated_count' => 3,
            ]);

        foreach ($invoices as $invoice) {
            $this->assertDatabaseHas('invoices', [
                'id' => $invoice->id,
                'status' => InvoiceStatus::PAID->value,
            ]);
        }
    }

    public function test_it_validates_bulk_update_request(): void
    {
        $response = $this->postJson('/api/v1/admin/invoices/bulk/update-status', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids', 'status']);
    }

    public function test_it_validates_invoice_ids_exist(): void
    {
        $data = [
            'ids' => ['nonexistent-id-1', 'nonexistent-id-2'],
            'status' => InvoiceStatus::PAID->value,
        ];

        $response = $this->postJson('/api/v1/admin/invoices/bulk/update-status', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids.0', 'ids.1']);
    }

    public function test_it_validates_status_enum(): void
    {
        $invoice = Invoice::factory()->create(['company_id' => $this->company->id]);

        $data = [
            'ids' => [$invoice->id],
            'status' => 'invalid-status',
        ];

        $response = $this->postJson('/api/v1/admin/invoices/bulk/update-status', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_it_requires_authentication(): void
    {
        // Create a fresh application instance without authentication
        $this->refreshApplication();

        $data = [
            'ids' => ['some-id'],
            'status' => InvoiceStatus::PAID->value,
        ];

        $response = $this->postJson('/api/v1/admin/invoices/bulk/update-status', $data);

        $response->assertStatus(401);
    }

    public function test_it_requires_permissions(): void
    {
        $this->user->revokePermissionTo('invoice.update');

        $data = [
            'ids' => ['some-id'],
            'status' => InvoiceStatus::PAID->value,
        ];

        $response = $this->postJson('/api/v1/admin/invoices/bulk/update-status', $data);

        $response->assertStatus(403);
    }
}
