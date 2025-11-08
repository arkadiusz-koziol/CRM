<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Migrations;

use App\Enums\Crm\OpportunityStatus;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpportunityMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_opportunities_table_has_correct_structure(): void
    {
        $this->assertTrue(\Schema::hasTable('opportunities'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'id'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'title'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'company_id'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'contact_id'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'value'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'currency'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'probability'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'stage_id'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'owner_user_id'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'close_date'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'status'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'created_at'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'updated_at'));
        $this->assertTrue(\Schema::hasColumn('opportunities', 'deleted_at'));
    }

    public function test_opportunities_table_accepts_valid_data(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);
        $contact = Contact::factory()->create(['owner_user_id' => $user->id]);
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);
        $stage = Stage::factory()->create(['pipeline_id' => $pipeline->id]);

        $opportunity = Opportunity::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'title' => 'Enterprise Software License',
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'value' => 50000.00,
            'currency' => 'USD',
            'probability' => 75,
            'stage_id' => $stage->id,
            'owner_user_id' => $user->id,
            'close_date' => '2024-12-31',
            'status' => OpportunityStatus::OPEN->value,
        ]);

        $this->assertDatabaseHas('opportunities', [
            'title' => 'Enterprise Software License',
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'value' => 50000.00,
            'currency' => 'USD',
            'probability' => 75,
            'stage_id' => $stage->id,
            'owner_user_id' => $user->id,
            'close_date' => '2024-12-31',
            'status' => OpportunityStatus::OPEN->value,
        ]);
    }

    public function test_opportunities_table_accepts_nullable_contact_and_close_date(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);
        $stage = Stage::factory()->create(['pipeline_id' => $pipeline->id]);

        $opportunity = Opportunity::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'title' => 'Small Project',
            'company_id' => $company->id,
            'contact_id' => null,
            'value' => 5000.00,
            'currency' => 'EUR',
            'probability' => 50,
            'stage_id' => $stage->id,
            'owner_user_id' => $user->id,
            'close_date' => null,
            'status' => OpportunityStatus::OPEN->value,
        ]);

        $this->assertDatabaseHas('opportunities', [
            'title' => 'Small Project',
            'company_id' => $company->id,
            'contact_id' => null,
            'value' => 5000.00,
            'currency' => 'EUR',
            'probability' => 50,
            'stage_id' => $stage->id,
            'owner_user_id' => $user->id,
            'close_date' => null,
            'status' => OpportunityStatus::OPEN->value,
        ]);
    }

    public function test_opportunities_table_soft_deletes(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);
        $stage = Stage::factory()->create(['pipeline_id' => $pipeline->id]);

        $opportunity = Opportunity::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'title' => 'Soft Delete Test',
            'company_id' => $company->id,
            'contact_id' => null,
            'value' => 10000.00,
            'currency' => 'USD',
            'probability' => 50,
            'stage_id' => $stage->id,
            'owner_user_id' => $user->id,
            'close_date' => null,
            'status' => OpportunityStatus::OPEN->value,
        ]);

        $opportunity->delete();

        $this->assertSoftDeleted('opportunities', [
            'id' => $opportunity->id,
        ]);
    }

    public function test_opportunities_table_foreign_key_constraints(): void
    {
        $user = User::factory()->create();

        // Test company_id foreign key
        $this->expectException(\Illuminate\Database\QueryException::class);

        Opportunity::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'title' => 'Invalid Opportunity',
            'company_id' => 'non-existent-company-id',
            'contact_id' => null,
            'value' => 10000.00,
            'currency' => 'USD',
            'probability' => 50,
            'stage_id' => 'non-existent-stage-id',
            'owner_user_id' => $user->id,
            'close_date' => null,
            'status' => OpportunityStatus::OPEN->value,
        ]);
    }

    public function test_opportunities_table_handles_all_statuses(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);
        $stage = Stage::factory()->create(['pipeline_id' => $pipeline->id]);

        $statuses = [
            OpportunityStatus::OPEN,
            OpportunityStatus::WON,
            OpportunityStatus::LOST,
        ];

        foreach ($statuses as $status) {
            $opportunity = Opportunity::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'title' => "Test Opportunity {$status->value}",
                'company_id' => $company->id,
                'contact_id' => null,
                'value' => 10000.00,
                'currency' => 'USD',
                'probability' => 50,
                'stage_id' => $stage->id,
                'owner_user_id' => $user->id,
                'close_date' => null,
                'status' => $status->value,
            ]);

            $this->assertDatabaseHas('opportunities', [
                'id' => $opportunity->id,
                'status' => $status->value,
            ]);
        }
    }

    public function test_opportunities_table_handles_different_currencies(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);
        $stage = Stage::factory()->create(['pipeline_id' => $pipeline->id]);

        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CAD'];

        foreach ($currencies as $currency) {
            $opportunity = Opportunity::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'title' => "Test Opportunity {$currency}",
                'company_id' => $company->id,
                'contact_id' => null,
                'value' => 10000.00,
                'currency' => $currency,
                'probability' => 50,
                'stage_id' => $stage->id,
                'owner_user_id' => $user->id,
                'close_date' => null,
                'status' => OpportunityStatus::OPEN->value,
            ]);

            $this->assertDatabaseHas('opportunities', [
                'id' => $opportunity->id,
                'currency' => $currency,
            ]);
        }
    }

    public function test_opportunities_table_handles_probability_bounds(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);
        $stage = Stage::factory()->create(['pipeline_id' => $pipeline->id]);

        $probabilities = [0, 25, 50, 75, 100];

        foreach ($probabilities as $probability) {
            $opportunity = Opportunity::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'title' => "Test Opportunity {$probability}%",
                'company_id' => $company->id,
                'contact_id' => null,
                'value' => 10000.00,
                'currency' => 'USD',
                'probability' => $probability,
                'stage_id' => $stage->id,
                'owner_user_id' => $user->id,
                'close_date' => null,
                'status' => OpportunityStatus::OPEN->value,
            ]);

            $this->assertDatabaseHas('opportunities', [
                'id' => $opportunity->id,
                'probability' => $probability,
            ]);
        }
    }
}
