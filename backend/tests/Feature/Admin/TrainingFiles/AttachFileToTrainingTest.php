<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingFiles;

use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AttachFileToTrainingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo('training.file.attach');
    }

    public function test_admin_can_attach_file_to_training(): void
    {
        $training = Training::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/files", [
            'file' => $file,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'original_name',
                        'file_name',
                        'file_path',
                        'mime_type',
                        'file_size',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('training_files', [
            'training_id' => $training->id,
            'original_name' => 'document.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $filePath = $response->json('data.attributes.file_path');
        Storage::disk('public')->assertExists($filePath);
    }

    public function test_admin_cannot_attach_file_to_non_existent_training(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/trainings/99999/files', [
            'file' => $file,
        ]);

        $response->assertNotFound()
            ->assertJson(['message' => __('app.training.not_found')]);
    }

    public function test_admin_cannot_attach_invalid_file_type(): void
    {
        $training = Training::factory()->create();
        $file = UploadedFile::fake()->create('document.txt', 1000, 'text/plain');

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/files", [
            'file' => $file,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['file']);
    }

    public function test_admin_cannot_attach_file_larger_than_limit(): void
    {
        $training = Training::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 11000, 'application/pdf'); // 11MB

        $response = $this->actingAs($this->admin)->postJson("/api/v1/admin/trainings/{$training->id}/files", [
            'file' => $file,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['file']);
    }

    public function test_unauthenticated_user_cannot_attach_file_to_training(): void
    {
        $training = Training::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->postJson("/api/v1/admin/trainings/{$training->id}/files", [
            'file' => $file,
        ]);

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_attach_file_to_training(): void
    {
        $user = User::factory()->create();
        $training = Training::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user)->postJson("/api/v1/admin/trainings/{$training->id}/files", [
            'file' => $file,
        ]);

        $response->assertForbidden();
    }
}
