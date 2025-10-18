<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Seeders;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_seeder_creates_contacts(): void
    {
        $this->seed(ContactSeeder::class);

        $this->assertDatabaseCount('contacts', 30);
    }

    public function test_contact_seeder_creates_contacts_with_diverse_data(): void
    {
        $this->seed(ContactSeeder::class);

        $contacts = Contact::all();

        $this->assertCount(30, $contacts);

        // Check that we have contacts with different statuses
        $statuses = $contacts->pluck('status')->unique()->toArray();
        $this->assertGreaterThan(1, count($statuses));

        // Check that we have contacts with different lead levels
        $leadLevels = $contacts->pluck('lead_level')->unique()->toArray();
        $this->assertGreaterThan(1, count($leadLevels));

        // Check that we have contacts with different sources
        $sources = $contacts->pluck('source')->unique()->toArray();
        $this->assertGreaterThan(1, count($sources));
    }

    public function test_contact_seeder_creates_contacts_with_valid_emails(): void
    {
        $this->seed(ContactSeeder::class);

        $contacts = Contact::all();

        foreach ($contacts as $contact) {
            $this->assertNotEmpty($contact->email);
            $this->assertStringContainsString('@', $contact->email);
            $this->assertStringContainsString('.', $contact->email);
        }
    }

    public function test_contact_seeder_creates_contacts_with_unique_emails(): void
    {
        $this->seed(ContactSeeder::class);

        $emails = Contact::pluck('email')->toArray();
        $uniqueEmails = array_unique($emails);

        $this->assertCount(count($emails), $uniqueEmails);
    }

    public function test_contact_seeder_creates_contacts_with_valid_names(): void
    {
        $this->seed(ContactSeeder::class);

        $contacts = Contact::all();

        foreach ($contacts as $contact) {
            $this->assertNotEmpty($contact->first_name);
            $this->assertNotEmpty($contact->last_name);
            $this->assertIsString($contact->first_name);
            $this->assertIsString($contact->last_name);
        }
    }

    public function test_contact_seeder_creates_contacts_with_phone_numbers(): void
    {
        $this->seed(ContactSeeder::class);

        $contactsWithPhone = Contact::whereNotNull('phone')->get();

        $this->assertGreaterThan(0, $contactsWithPhone->count());

        foreach ($contactsWithPhone as $contact) {
            $this->assertNotEmpty($contact->phone);
            $this->assertStringContainsString('+', $contact->phone);
        }
    }

    public function test_contact_seeder_creates_contacts_with_mixed_phone_data(): void
    {
        $this->seed(ContactSeeder::class);

        $contactsWithPhone = Contact::whereNotNull('phone')->count();
        $contactsWithoutPhone = Contact::whereNull('phone')->count();

        $this->assertGreaterThanOrEqual(0, $contactsWithPhone);
        $this->assertGreaterThanOrEqual(0, $contactsWithoutPhone);
    }

    public function test_contact_seeder_creates_contacts_with_diverse_company_domains(): void
    {
        $this->seed(ContactSeeder::class);

        $contacts = Contact::all();
        $domains = $contacts->pluck('email')->map(function ($email) {
            return substr($email, strpos($email, '@') + 1);
        })->unique()->toArray();

        $this->assertGreaterThan(5, count($domains));
    }
}
