<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Crm\OpportunityStatus;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OpportunityApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Company $company;

    private Contact $contact;

    private Pipeline $pipeline;

    private Stage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure all migrations are run in correct order
        $this->artisan('migrate');
        $this->seed();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['created_by' => $this->user->id]);
        $this->contact = Contact::factory()->create(['owner_user_id' => $this->user->id]);
        $this->pipeline = Pipeline::factory()->create(['created_by' => $this->user->id]);
        $this->stage = Stage::factory()->create(['pipeline_id' => $this->pipeline->id]);

        Sanctum::actingAs($this->user);
    }

    public function test_can_list_opportunities(): void
    {
        Opportunity::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/admin/opportunities');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'data' => [
                            'type',
                            'id',
                            'attributes' => [
                                'title',
                                'company_id',
                                'value',
                                'currency',
                                'probability',
                                'stage_id',
                                'owner_user_id',
                                'status',
                            ],
                        ],
                    ],
                ],
                'meta',
            ]);
    }

    public function test_can_create_opportunity(): void
    {
        $opportunityData = [
            'title' => 'Enterprise Software License',
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'value' => 50000.00,
            'currency' => 'USD',
            'probability' => 75,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
            'close_date' => '2024-12-31',
        ];

        $response = $this->postJson('/api/admin/opportunities', $opportunityData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        'type',
                        'id',
                        'attributes',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('opportunities', [
            'title' => 'Enterprise Software License',
            'company_id' => $this->company->id,
            'value' => 50000.00,
            'currency' => 'USD',
            'probability' => 75,
        ]);
    }

    public function test_can_show_opportunity(): void
    {
        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/admin/opportunities/{$opportunity->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        'type',
                        'id',
                        'attributes',
                    ],
                ],
            ]);
    }

    public function test_can_update_opportunity(): void
    {
        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $updateData = [
            'title' => 'Updated Opportunity Title',
            'value' => 75000.00,
            'probability' => 90,
        ];

        $response = $this->putJson("/api/admin/opportunities/{$opportunity->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('opportunities', [
            'id' => $opportunity->id,
            'title' => 'Updated Opportunity Title',
            'value' => 75000.00,
            'probability' => 90,
        ]);
    }

    public function test_can_delete_opportunity(): void
    {
        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/admin/opportunities/{$opportunity->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('opportunities', [
            'id' => $opportunity->id,
        ]);
    }

    public function test_can_filter_opportunities_by_owner(): void
    {
        $otherUser = User::factory()->create();

        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $otherUser->id,
        ]);

        $response = $this->getJson("/api/admin/opportunities?owner={$this->user->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_filter_opportunities_by_company(): void
    {
        $otherCompany = Company::factory()->create(['created_by' => $this->user->id]);

        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        Opportunity::factory()->create([
            'company_id' => $otherCompany->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/admin/opportunities?company={$this->company->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_filter_opportunities_by_stage(): void
    {
        $otherStage = Stage::factory()->create(['pipeline_id' => $this->pipeline->id]);

        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $otherStage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/admin/opportunities?stage={$this->stage->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_filter_opportunities_by_status(): void
    {
        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
            'status' => OpportunityStatus::OPEN->value,
        ]);

        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
            'status' => OpportunityStatus::WON->value,
        ]);

        $response = $this->getJson('/api/admin/opportunities?status='.OpportunityStatus::OPEN->value);

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_search_opportunities(): void
    {
        Opportunity::factory()->create([
            'title' => 'Software License Deal',
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        Opportunity::factory()->create([
            'title' => 'Hardware Purchase',
            'company_id' => $this->company->id,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/admin/opportunities?search=Software');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_validation_requires_title(): void
    {
        $response = $this->postJson('/api/admin/opportunities', [
            'company_id' => $this->company->id,
            'value' => 50000.00,
            'currency' => 'USD',
            'probability' => 75,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_validation_requires_company_id(): void
    {
        $response = $this->postJson('/api/admin/opportunities', [
            'title' => 'Test Opportunity',
            'value' => 50000.00,
            'currency' => 'USD',
            'probability' => 75,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['company_id']);
    }

    public function test_validation_requires_value_greater_than_zero(): void
    {
        $response = $this->postJson('/api/admin/opportunities', [
            'title' => 'Test Opportunity',
            'company_id' => $this->company->id,
            'value' => -1000.00,
            'currency' => 'USD',
            'probability' => 75,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['value']);
    }

    public function test_validation_requires_probability_between_0_and_100(): void
    {
        $response = $this->postJson('/api/admin/opportunities', [
            'title' => 'Test Opportunity',
            'company_id' => $this->company->id,
            'value' => 50000.00,
            'currency' => 'USD',
            'probability' => 150,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['probability']);
    }

    public function test_validation_requires_close_date_in_future(): void
    {
        $response = $this->postJson('/api/admin/opportunities', [
            'title' => 'Test Opportunity',
            'company_id' => $this->company->id,
            'value' => 50000.00,
            'currency' => 'USD',
            'probability' => 75,
            'stage_id' => $this->stage->id,
            'owner_user_id' => $this->user->id,
            'close_date' => '2020-01-01',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['close_date']);
    }

    public function test_returns_404_for_nonexistent_opportunity(): void
    {
        $response = $this->getJson('/api/admin/opportunities/non-existent-id');

        $response->assertStatus(404);
    }

    public function test_requires_authentication(): void
    {
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/admin/opportunities');

        $response->assertStatus(401);
    }
}
