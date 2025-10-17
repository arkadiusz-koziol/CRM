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

final class AttachMultipleFilesToTrainingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->admin->givePermissionTo('training.file.attach');
    }

    public function test_admin_can_attach_multiple_files_to_training(): void
    {
        $training = Training::factory()->create();
        $files = [
            UploadedFile::fake()->create('document1.pdf', 1000, 'application/pdf'),
            UploadedFile::fake()->create('document2.pptx', 2000, 'application/vnd.openxmlformats-officedocument.presentationml.presentation'),
        ];

        $response = $this->actingAs($this->admin)->postJson("/v1/admin/trainings/{$training->id}/files/multiple", [
            'files' => $files,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes' => [
                            'original_name',
                            'file_name',
                            'file_path',
                            'mime_type',
                            'file_size',
                        ],
                    ],
                ],
            ]);

        $this->assertDatabaseCount('training_files', 2);
        $this->assertDatabaseHas('training_files', [
            'training_id' => $training->id,
            'original_name' => 'document1.pdf',
        ]);
        $this->assertDatabaseHas('training_files', [
            'training_id' => $training->id,
            'original_name' => 'document2.pptx',
        ]);
    }

    public function test_admin_cannot_attach_more_than_maximum_files(): void
    {
        $training = Training::factory()->create();
        $files = [];
        for ($i = 0; $i < 11; $i++) {
            $files[] = UploadedFile::fake()->create("document{$i}.pdf", 1000, 'application/pdf');
        }

        $response = $this->actingAs($this->admin)->postJson("/v1/admin/trainings/{$training->id}/files/multiple", [
            'files' => $files,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['files']);
    }

    public function test_admin_cannot_attach_empty_files_array(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->postJson("/v1/admin/trainings/{$training->id}/files/multiple", [
            'files' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['files']);
    }

    public function test_admin_cannot_attach_files_to_non_existent_training(): void
    {
        $files = [
            UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ];

        $response = $this->actingAs($this->admin)->postJson('/v1/admin/trainings/99999/files/multiple', [
            'files' => $files,
        ]);

        $response->assertNotFound()
            ->assertJson(['message' => __('app.training.not_found')]);
    }

    public function test_unauthenticated_user_cannot_attach_multiple_files_to_training(): void
    {
        $training = Training::factory()->create();
        $files = [
            UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ];

        $response = $this->postJson("/v1/admin/trainings/{$training->id}/files/multiple", [
            'files' => $files,
        ]);

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_attach_multiple_files_to_training(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $training = Training::factory()->create();
        $files = [
            UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ];

        $response = $this->actingAs($user)->postJson("/v1/admin/trainings/{$training->id}/files/multiple", [
            'files' => $files,
        ]);

        $response->assertForbidden();
    }
}
