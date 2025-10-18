<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Company;
use App\Models\User;
use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo([
            'contact.view',
            'contact.create',
            'contact.update',
            'contact.delete',
        ]);
    }

    public function test_can_list_contacts(): void
    {
        Contact::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes' => [
                            'first_name',
                            'last_name',
                            'full_name',
                            'email',
                            'phone',
                            'lead_level',
                            'owner_user_id',
                            'source',
                            'status',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'meta' => [
                    'pagination',
                    'request_id',
                ],
            ]);
    }

    public function test_can_create_contact(): void
    {
        $contactData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'lead_level' => LeadLevel::LEAD->value,
            'owner_user_id' => (string) $this->user->id,
            'source' => 'website',
            'status' => ContactStatus::NEW->value,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/admin/contacts', $contactData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'first_name',
                        'last_name',
                        'full_name',
                        'email',
                        'phone',
                        'lead_level',
                        'owner_user_id',
                        'source',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'request_id',
                ],
            ]);

        $this->assertDatabaseHas('contacts', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);
    }

    public function test_can_show_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/admin/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'first_name',
                        'last_name',
                        'full_name',
                        'email',
                        'phone',
                        'lead_level',
                        'owner_user_id',
                        'source',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'request_id',
                ],
            ]);
    }

    public function test_can_update_contact(): void
    {
        $contact = Contact::factory()->create();

        $updateData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'status' => ContactStatus::ACTIVE->value,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/admin/contacts/{$contact->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'status' => ContactStatus::ACTIVE->value,
        ]);
    }

    public function test_can_delete_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/v1/admin/contacts/{$contact->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_can_filter_contacts(): void
    {
        Contact::factory()->create(['status' => ContactStatus::ACTIVE->value]);
        Contact::factory()->create(['status' => ContactStatus::NEW->value]);
        Contact::factory()->create(['lead_level' => LeadLevel::LEAD->value]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/contacts?status=' . ContactStatus::ACTIVE->value);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/admin/contacts');
        $response->assertStatus(401);
    }

    public function test_requires_permission(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/admin/contacts');

        $response->assertStatus(403);
    }
}
