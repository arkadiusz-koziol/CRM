<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm\Entity;

use App\Domain\Crm\Entity\Contact;
use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use Carbon\Carbon;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_it_creates_contact_with_required_fields(): void
    {
        $contact = Contact::create(
            firstName: 'John',
            lastName: 'Smith',
            email: 'john.smith@techcorp.com',
            phone: '+1-555-0101',
            leadLevel: LeadLevel::CONTACT,
            ownerUserId: 'user-123',
            source: 'website',
            status: ContactStatus::ACTIVE
        );

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertNotEmpty($contact->id());
        $this->assertEquals('John', $contact->firstName());
        $this->assertEquals('Smith', $contact->lastName());
        $this->assertEquals('John Smith', $contact->fullName());
        $this->assertEquals('john.smith@techcorp.com', $contact->email());
        $this->assertEquals('+1-555-0101', $contact->phone());
        $this->assertEquals(LeadLevel::CONTACT, $contact->leadLevel());
        $this->assertEquals('user-123', $contact->ownerUserId());
        $this->assertEquals('website', $contact->source());
        $this->assertEquals(ContactStatus::ACTIVE, $contact->status());
        $this->assertInstanceOf(Carbon::class, $contact->createdAt());
        $this->assertInstanceOf(Carbon::class, $contact->updatedAt());
        $this->assertNull($contact->deletedAt());
        $this->assertFalse($contact->isDeleted());
    }

    public function test_it_creates_contact_with_minimal_fields(): void
    {
        $contact = Contact::create(
            firstName: 'Jane',
            lastName: 'Doe',
            email: 'jane.doe@example.com',
            phone: null,
            leadLevel: LeadLevel::LEAD,
            ownerUserId: null,
            source: 'referral',
            status: ContactStatus::NEW
        );

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('Jane', $contact->firstName());
        $this->assertEquals('Doe', $contact->lastName());
        $this->assertEquals('Jane Doe', $contact->fullName());
        $this->assertEquals('jane.doe@example.com', $contact->email());
        $this->assertNull($contact->phone());
        $this->assertEquals(LeadLevel::LEAD, $contact->leadLevel());
        $this->assertNull($contact->ownerUserId());
        $this->assertEquals('referral', $contact->source());
        $this->assertEquals(ContactStatus::NEW, $contact->status());
    }

    public function test_it_uses_uuid7_for_id(): void
    {
        $contact = Contact::create(
            firstName: 'Test',
            lastName: 'User',
            email: 'test@example.com',
            phone: null,
            leadLevel: LeadLevel::LEAD,
            ownerUserId: null,
            source: 'website',
            status: ContactStatus::NEW
        );

        $id = $contact->id();
        $this->assertIsString($id);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id);
    }

    public function test_it_sets_created_and_updated_at_to_current_time(): void
    {
        $before = Carbon::now();

        $contact = Contact::create(
            firstName: 'Time',
            lastName: 'Test',
            email: 'time@test.com',
            phone: null,
            leadLevel: LeadLevel::LEAD,
            ownerUserId: null,
            source: 'website',
            status: ContactStatus::NEW
        );

        $after = Carbon::now();

        $this->assertTrue($contact->createdAt()->between($before, $after));
        $this->assertTrue($contact->updatedAt()->between($before, $after));
    }

    public function test_it_handles_all_lead_levels(): void
    {
        $leadLevels = [
            LeadLevel::LEAD,
            LeadLevel::CONTACT,
        ];

        foreach ($leadLevels as $leadLevel) {
            $contact = Contact::create(
                firstName: 'Test',
                lastName: 'User',
                email: "test{$leadLevel->value}@example.com",
                phone: null,
                leadLevel: $leadLevel,
                ownerUserId: null,
                source: 'website',
                status: ContactStatus::NEW
            );

            $this->assertEquals($leadLevel, $contact->leadLevel());
        }
    }

    public function test_it_handles_all_contact_statuses(): void
    {
        $statuses = [
            ContactStatus::NEW,
            ContactStatus::ACTIVE,
            ContactStatus::DORMANT,
            ContactStatus::LOST,
        ];

        foreach ($statuses as $status) {
            $contact = Contact::create(
                firstName: 'Test',
                lastName: 'User',
                email: "test{$status->value}@example.com",
                phone: null,
                leadLevel: LeadLevel::LEAD,
                ownerUserId: null,
                source: 'website',
                status: $status
            );

            $this->assertEquals($status, $contact->status());
        }
    }

    public function test_full_name_concatenation(): void
    {
        $contact = Contact::create(
            firstName: 'John',
            lastName: 'Smith',
            email: 'john.smith@example.com',
            phone: null,
            leadLevel: LeadLevel::LEAD,
            ownerUserId: null,
            source: 'website',
            status: ContactStatus::NEW
        );

        $this->assertEquals('John Smith', $contact->fullName());
    }
}
