<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Training;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class TrainingCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_create_training_without_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $trainingData = [
            'title' => 'Safety Training',
            'description' => 'Comprehensive safety training course',
            'category' => 'Safety',
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(201)
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
                    'title' => 'Safety Training',
                    'description' => 'Comprehensive safety training course',
                    'category' => 'Safety',
                    'file_path' => null,
                    'file_name' => null,
                    'file_size' => null,
                    'mime_type' => null,
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'title' => 'Safety Training',
            'description' => 'Comprehensive safety training course',
            'category' => 'Safety',
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
        ]);
    }

    public function test_admin_can_create_training_with_pdf_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $file = UploadedFile::fake()->create('training.pdf', 1024, 'application/pdf');

        $trainingData = [
            'title' => 'PDF Training',
            'description' => 'Training with PDF file',
            'category' => 'Documentation',
            'file' => $file,
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(201)
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
                    'title' => 'PDF Training',
                    'description' => 'Training with PDF file',
                    'category' => 'Documentation',
                    'file_name' => 'training.pdf',
                    'mime_type' => 'application/pdf',
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'title' => 'PDF Training',
            'description' => 'Training with PDF file',
            'category' => 'Documentation',
            'file_name' => 'training.pdf',
            'mime_type' => 'application/pdf',
        ]);

        Storage::disk('public')->assertExists($response->json('data.file_path'));
    }

    public function test_admin_can_create_training_with_pptx_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $file = UploadedFile::fake()->create('presentation.pptx', 2048, 'application/vnd.openxmlformats-officedocument.presentationml.presentation');

        $trainingData = [
            'title' => 'PowerPoint Training',
            'description' => 'Training with PowerPoint file',
            'category' => 'Presentation',
            'file' => $file,
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'PowerPoint Training',
                    'description' => 'Training with PowerPoint file',
                    'category' => 'Presentation',
                    'file_name' => 'presentation.pptx',
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'title' => 'PowerPoint Training',
            'description' => 'Training with PowerPoint file',
            'category' => 'Presentation',
            'file_name' => 'presentation.pptx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);

        Storage::disk('public')->assertExists($response->json('data.file_path'));
    }

    public function test_admin_can_create_training_with_null_description(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $trainingData = [
            'title' => 'Simple Training',
            'description' => null,
            'category' => 'Basic',
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'Simple Training',
                    'description' => null,
                    'category' => 'Basic',
                ],
            ]);

        $this->assertDatabaseHas('trainings', [
            'title' => 'Simple Training',
            'description' => null,
            'category' => 'Basic',
        ]);
    }

    public function test_unauthorized_user_cannot_create_training(): void
    {
        $user = User::factory()->create();

        $trainingData = [
            'title' => 'Unauthorized Training',
            'description' => 'This should fail',
            'category' => 'Test',
        ];

        $response = $this->actingAs($user)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_create_training(): void
    {
        $trainingData = [
            'title' => 'Unauthenticated Training',
            'description' => 'This should fail',
            'category' => 'Test',
        ];

        $response = $this->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(401);
    }

    public function test_create_training_validates_required_fields(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'category']);
    }

    public function test_create_training_validates_title_max_length(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $trainingData = [
            'title' => str_repeat('a', 256),
            'category' => 'Test',
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_create_training_validates_category_max_length(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $trainingData = [
            'title' => 'Test Training',
            'category' => str_repeat('a', 101),
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);
    }

    public function test_create_training_validates_file_mime_type(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $file = UploadedFile::fake()->create('document.txt', 1024, 'text/plain');

        $trainingData = [
            'title' => 'Invalid File Training',
            'category' => 'Test',
            'file' => $file,
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    public function test_create_training_validates_file_size(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $file = UploadedFile::fake()->create('large.pdf', 11264, 'application/pdf');

        $trainingData = [
            'title' => 'Large File Training',
            'category' => 'Test',
            'file' => $file,
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    public function test_create_training_handles_database_errors_gracefully(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $this->mock(\App\Services\TrainingService::class, function ($mock) {
            $mock->shouldReceive('createTraining')
                ->andThrow(new \Exception('Database connection failed'));
        });

        $trainingData = [
            'title' => 'Error Training',
            'category' => 'Test',
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(500)
            ->assertJson([
                'message' => __('app.action.failed'),
            ]);
    }

    public function test_create_training_returns_correct_json_structure(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.create');

        $trainingData = [
            'title' => 'JSON Structure Test',
            'description' => 'Testing JSON response structure',
            'category' => 'Testing',
        ];

        $response = $this->actingAs($admin)
            ->postJson('/v1/admin/trainings', $trainingData);

        $response->assertStatus(201)
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
}
