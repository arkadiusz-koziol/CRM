<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Training;

use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class TrainingUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_update_training_without_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'category' => 'Original Category',
        ]);

        $updateData = [
            'title' => 'Updated Safety Training',
            'description' => 'Updated comprehensive safety training course',
            'category' => 'Safety',
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'category',
                    'file_path',
                    'file_name',
                    'file_size',
                    'mime_type',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => $training->id,
                    'title' => 'Updated Safety Training',
                    'description' => 'Updated comprehensive safety training course',
                    'category' => 'Safety',
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'title' => 'Updated Safety Training',
            'description' => 'Updated comprehensive safety training course',
            'category' => 'Safety',
        ]);
    }

    public function test_admin_can_update_training_with_new_pdf_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create([
            'title' => 'Original Title',
            'file_name' => 'old.pdf',
            'file_path' => 'trainings/old.pdf',
        ]);

        $file = UploadedFile::fake()->create('new-training.pdf', 1024, 'application/pdf');

        $updateData = [
            'title' => 'Updated PDF Training',
            'description' => 'Training with new PDF file',
            'category' => 'Documentation',
            'file' => $file,
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $training->id,
                    'title' => 'Updated PDF Training',
                    'description' => 'Training with new PDF file',
                    'category' => 'Documentation',
                    'file_name' => 'new-training.pdf',
                    'mime_type' => 'application/pdf',
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'title' => 'Updated PDF Training',
            'description' => 'Training with new PDF file',
            'category' => 'Documentation',
            'file_name' => 'new-training.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $this->assertNotNull($response->json('data.file_path'));
    }

    public function test_admin_can_update_training_with_new_pptx_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create([
            'title' => 'Original Title',
        ]);

        $file = UploadedFile::fake()->create('new-presentation.pptx', 2048, 'application/vnd.openxmlformats-officedocument.presentationml.presentation');

        $updateData = [
            'title' => 'Updated PowerPoint Training',
            'description' => 'Training with new PowerPoint file',
            'category' => 'Presentation',
            'file' => $file,
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $training->id,
                    'title' => 'Updated PowerPoint Training',
                    'description' => 'Training with new PowerPoint file',
                    'category' => 'Presentation',
                    'file_name' => 'new-presentation.pptx',
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'title' => 'Updated PowerPoint Training',
            'description' => 'Training with new PowerPoint file',
            'category' => 'Presentation',
            'file_name' => 'new-presentation.pptx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);

        $this->assertNotNull($response->json('data.file_path'));
    }

    public function test_admin_can_update_training_with_null_description(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
        ]);

        $updateData = [
            'title' => 'Updated Simple Training',
            'description' => null,
            'category' => 'Basic',
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $training->id,
                    'title' => 'Updated Simple Training',
                    'description' => null,
                    'category' => 'Basic',
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'title' => 'Updated Simple Training',
            'description' => null,
            'category' => 'Basic',
        ]);
    }

    public function test_unauthorized_user_cannot_update_training(): void
    {
        $user = User::factory()->create();

        $training = Training::factory()->create();

        $updateData = [
            'title' => 'Unauthorized Update',
            'category' => 'Test',
        ];

        $response = $this->actingAs($user)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_update_training(): void
    {
        $training = Training::factory()->create();

        $updateData = [
            'title' => 'Unauthenticated Update',
            'category' => 'Test',
        ];

        $response = $this->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(401);
    }

    public function test_update_training_validates_required_fields(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create();

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'category']);
    }

    public function test_update_training_validates_title_max_length(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create();

        $updateData = [
            'title' => str_repeat('a', 256),
            'category' => 'Test',
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_update_training_validates_category_max_length(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create();

        $updateData = [
            'title' => 'Test Training',
            'category' => str_repeat('a', 101),
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);
    }

    public function test_update_training_validates_file_mime_type(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create();

        $file = UploadedFile::fake()->create('document.txt', 1024, 'text/plain');

        $updateData = [
            'title' => 'Invalid File Training',
            'category' => 'Test',
            'file' => $file,
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    public function test_update_training_validates_file_size(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create();

        $file = UploadedFile::fake()->create('large.pdf', 11264, 'application/pdf');

        $updateData = [
            'title' => 'Large File Training',
            'category' => 'Test',
            'file' => $file,
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    public function test_update_training_handles_database_errors_gracefully(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create();

        $this->mock(\App\Services\TrainingService::class, function ($mock) {
            $mock->shouldReceive('updateTraining')
                ->andThrow(new \Exception('Database connection failed'));
        });

        $updateData = [
            'title' => 'Error Training',
            'category' => 'Test',
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(500)
            ->assertJson([
                'message' => __('app.action.failed'),
            ]);
    }

    public function test_update_training_returns_correct_json_structure(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create();

        $updateData = [
            'title' => 'JSON Structure Test',
            'description' => 'Testing JSON response structure',
            'category' => 'Testing',
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'category',
                    'file_path',
                    'file_name',
                    'file_size',
                    'mime_type',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $responseData = $response->json('data');
        $this->assertNotNull($responseData['id']);
        $this->assertNotNull($responseData['created_at']);
        $this->assertNotNull($responseData['updated_at']);
    }

    public function test_update_training_preserves_existing_file_when_no_new_file_provided(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.update');

        $training = Training::factory()->create([
            'title' => 'Original Title',
            'file_name' => 'existing.pdf',
            'file_path' => 'trainings/existing.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'category' => 'Updated Category',
        ];

        $response = $this->actingAs($admin)
            ->putJson("/v1/admin/trainings/{$training->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $training->id,
                    'title' => 'Updated Title',
                    'category' => 'Updated Category',
                    'file_name' => 'existing.pdf',
                    'file_path' => 'trainings/existing.pdf',
                    'file_size' => 1024,
                    'mime_type' => 'application/pdf',
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'title' => 'Updated Title',
            'category' => 'Updated Category',
            'file_name' => 'existing.pdf',
            'file_path' => 'trainings/existing.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);
    }
}
