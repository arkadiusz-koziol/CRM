<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\OpportunityProbabilityChanged;
use App\Events\OpportunityStageChanged;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KanbanApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Company $company;

    private Contact $contact;

    private Pipeline $pipeline;

    private Stage $stage1;

    private Stage $stage2;

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
        $this->stage1 = Stage::factory()->create(['pipeline_id' => $this->pipeline->id, 'order' => 1]);
        $this->stage2 = Stage::factory()->create(['pipeline_id' => $this->pipeline->id, 'order' => 2]);

        Sanctum::actingAs($this->user);
    }

    public function test_can_get_kanban_data(): void
    {
        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
        ]);

        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage2->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/admin/kanban/opportunities');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'data' => [
                            'type',
                            'id',
                            'attributes' => [
                                'stage_id',
                                'stage_name',
                                'opportunities',
                                'total_opportunities',
                            ],
                        ],
                    ],
                ],
                'meta',
            ]);
    }

    public function test_can_move_opportunity_to_different_stage(): void
    {
        Event::fake();

        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->postJson('/api/admin/kanban/opportunities/move-stage', [
            'opportunity_id' => $opportunity->id,
            'new_stage_id' => $this->stage2->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Opportunity stage updated successfully',
            ]);

        $this->assertDatabaseHas('opportunities', [
            'id' => $opportunity->id,
            'stage_id' => $this->stage2->id,
        ]);

        Event::assertDispatched(OpportunityStageChanged::class);
    }

    public function test_can_update_opportunity_probability(): void
    {
        Event::fake();

        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
            'probability' => 50,
        ]);

        $response = $this->postJson('/api/admin/kanban/opportunities/update-probability', [
            'opportunity_id' => $opportunity->id,
            'probability' => 75,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Opportunity probability updated successfully',
            ]);

        $this->assertDatabaseHas('opportunities', [
            'id' => $opportunity->id,
            'probability' => 75,
        ]);

        Event::assertDispatched(OpportunityProbabilityChanged::class);
    }

    public function test_move_stage_validation_requires_opportunity_id(): void
    {
        $response = $this->postJson('/api/admin/kanban/opportunities/move-stage', [
            'new_stage_id' => $this->stage2->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['opportunity_id']);
    }

    public function test_move_stage_validation_requires_new_stage_id(): void
    {
        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->postJson('/api/admin/kanban/opportunities/move-stage', [
            'opportunity_id' => $opportunity->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['new_stage_id']);
    }

    public function test_update_probability_validation_requires_opportunity_id(): void
    {
        $response = $this->postJson('/api/admin/kanban/opportunities/update-probability', [
            'probability' => 75,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['opportunity_id']);
    }

    public function test_update_probability_validation_requires_probability(): void
    {
        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->postJson('/api/admin/kanban/opportunities/update-probability', [
            'opportunity_id' => $opportunity->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['probability']);
    }

    public function test_update_probability_validation_requires_probability_between_0_and_100(): void
    {
        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->postJson('/api/admin/kanban/opportunities/update-probability', [
            'opportunity_id' => $opportunity->id,
            'probability' => 150,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['probability']);
    }

    public function test_kanban_groups_opportunities_by_stage(): void
    {
        // Create opportunities in different stages
        Opportunity::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
        ]);

        Opportunity::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage2->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/admin/kanban/opportunities');

        $response->assertStatus(200);

        $data = $response->json('data');

        // Should have 2 columns (stages)
        $this->assertCount(2, $data);

        // Find the columns and verify counts
        $stage1Column = collect($data)->firstWhere('data.attributes.stage_id', $this->stage1->id);
        $stage2Column = collect($data)->firstWhere('data.attributes.stage_id', $this->stage2->id);

        $this->assertNotNull($stage1Column);
        $this->assertNotNull($stage2Column);
        $this->assertEquals(2, $stage1Column['data']['attributes']['total_opportunities']);
        $this->assertEquals(3, $stage2Column['data']['attributes']['total_opportunities']);
    }

    public function test_requires_authentication_for_kanban(): void
    {
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/admin/kanban/opportunities');

        $response->assertStatus(401);
    }

    public function test_requires_authentication_for_move_stage(): void
    {
        Sanctum::actingAs(null);

        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->postJson('/api/admin/kanban/opportunities/move-stage', [
            'opportunity_id' => $opportunity->id,
            'new_stage_id' => $this->stage2->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_requires_authentication_for_update_probability(): void
    {
        Sanctum::actingAs(null);

        $opportunity = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'stage_id' => $this->stage1->id,
            'owner_user_id' => $this->user->id,
        ]);

        $response = $this->postJson('/api/admin/kanban/opportunities/update-probability', [
            'opportunity_id' => $opportunity->id,
            'probability' => 75,
        ]);

        $response->assertStatus(401);
    }
}
