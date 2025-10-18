<?php

declare(strict_types=1);

namespace Tests\Feature\Broadcasting;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_authenticates_user_channel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', [
                'socket_id' => 'test-socket-id',
                'channel_name' => 'user.' . $user->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'auth'
            ]);
    }

    public function test_it_authenticates_entity_channel(): void
    {
        $user = User::factory()->create();
        
        // Create permission if it doesn't exist
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'company.view',
            'guard_name' => 'web'
        ]);
        $user->givePermissionTo($permission);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', [
                'socket_id' => 'test-socket-id',
                'channel_name' => 'entity.company.company-123',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'auth'
            ]);
    }

    public function test_it_authenticates_admin_channel(): void
    {
        $user = User::factory()->create();
        
        // Create admin role if it doesn't exist
        $role = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', [
                'socket_id' => 'test-socket-id',
                'channel_name' => 'admin',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'auth'
            ]);
    }

    public function test_it_rejects_unauthorized_user_channel(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', [
                'socket_id' => 'test-socket-id',
                'channel_name' => 'user.' . $otherUser->id,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Insufficient permissions for this channel',
                'errors' => [
                    'channel_name' => ['Access denied']
                ]
            ]);
    }

    public function test_it_rejects_unauthorized_entity_channel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', [
                'socket_id' => 'test-socket-id',
                'channel_name' => 'entity.company.company-123',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Insufficient permissions for this channel',
                'errors' => [
                    'channel_name' => ['Access denied']
                ]
            ]);
    }

    public function test_it_rejects_unauthorized_admin_channel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', [
                'socket_id' => 'test-socket-id',
                'channel_name' => 'admin',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Insufficient permissions for this channel',
                'errors' => [
                    'channel_name' => ['Access denied']
                ]
            ]);
    }

    public function test_it_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['socket_id', 'channel_name']);
    }

    public function test_it_validates_socket_id_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', [
                'socket_id' => str_repeat('a', 256),
                'channel_name' => 'user.' . $user->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['socket_id']);
    }

    public function test_it_validates_channel_name_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/broadcasting/auth', [
                'socket_id' => 'test-socket-id',
                'channel_name' => 'invalid@channel#name',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['channel_name']);
    }

    public function test_it_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/broadcasting/auth', [
            'socket_id' => 'test-socket-id',
            'channel_name' => 'user.123',
        ]);

        $response->assertStatus(401);
    }
}
