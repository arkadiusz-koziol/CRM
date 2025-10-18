<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Migrations;

use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_contacts_table_has_correct_structure(): void
    {
        $this->assertTrue(\Schema::hasTable('contacts'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'id'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'first_name'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'last_name'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'email'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'phone'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'lead_level'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'owner_user_id'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'source'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'status'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'created_at'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'updated_at'));
        $this->assertTrue(\Schema::hasColumn('contacts', 'deleted_at'));
    }

    public function test_contacts_table_accepts_valid_data(): void
    {
        $user = User::factory()->create();

        $contact = Contact::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@example.com',
            'phone' => '+1-555-0101',
            'lead_level' => LeadLevel::CONTACT->value,
            'owner_user_id' => $user->id,
            'source' => 'website',
            'status' => ContactStatus::ACTIVE->value,
        ]);

        $this->assertDatabaseHas('contacts', [
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@example.com',
            'phone' => '+1-555-0101',
            'lead_level' => LeadLevel::CONTACT->value,
            'owner_user_id' => $user->id,
            'source' => 'website',
            'status' => ContactStatus::ACTIVE->value,
        ]);
    }

    public function test_contacts_table_enforces_email_uniqueness(): void
    {
        Contact::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'first_name' => 'First',
            'last_name' => 'Contact',
            'email' => 'duplicate@example.com',
            'lead_level' => LeadLevel::LEAD->value,
            'source' => 'website',
            'status' => ContactStatus::NEW->value,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Contact::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'first_name' => 'Second',
            'last_name' => 'Contact',
            'email' => 'duplicate@example.com', // Duplicate email
            'lead_level' => LeadLevel::LEAD->value,
            'source' => 'website',
            'status' => ContactStatus::NEW->value,
        ]);
    }

    public function test_contacts_table_accepts_nullable_fields(): void
    {
        $contact = Contact::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'first_name' => 'Minimal',
            'last_name' => 'Contact',
            'email' => 'minimal@example.com',
            'phone' => null,
            'lead_level' => LeadLevel::LEAD->value,
            'owner_user_id' => null,
            'source' => 'website',
            'status' => ContactStatus::NEW->value,
        ]);

        $this->assertDatabaseHas('contacts', [
            'first_name' => 'Minimal',
            'last_name' => 'Contact',
            'email' => 'minimal@example.com',
            'phone' => null,
            'owner_user_id' => null,
        ]);
    }

    public function test_contacts_table_soft_deletes(): void
    {
        $contact = Contact::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'first_name' => 'Soft Delete',
            'last_name' => 'Test',
            'email' => 'softdelete@example.com',
            'lead_level' => LeadLevel::LEAD->value,
            'source' => 'website',
            'status' => ContactStatus::NEW->value,
        ]);

        $contact->delete();

        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_contacts_table_foreign_key_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Contact::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'first_name' => 'Invalid',
            'last_name' => 'Contact',
            'email' => 'invalid@example.com',
            'lead_level' => LeadLevel::LEAD->value,
            'owner_user_id' => 'non-existent-user-id',
            'source' => 'website',
            'status' => ContactStatus::NEW->value,
        ]);
    }
}
