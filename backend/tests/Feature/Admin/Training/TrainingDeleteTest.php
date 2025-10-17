<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Training;

use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class TrainingDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_delete_training_without_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        $training = Training::factory()->create([
            'title' => 'Training to Delete',
            'description' => 'This training will be deleted',
            'category' => 'Test Category',
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('trainings', [
            'id' => $training->id,
            'title' => 'Training to Delete',
            'description' => 'This training will be deleted',
            'category' => 'Test Category',
        ]);
    }

    public function test_admin_can_delete_training_with_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        // Create a fake file
        $filePath = 'trainings/test-training.pdf';
        Storage::disk('public')->put($filePath, 'fake file content');

        $training = Training::factory()->create([
            'title' => 'Training with File',
            'description' => 'This training has a file',
            'category' => 'File Category',
            'file_path' => $filePath,
            'file_name' => 'test-training.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('trainings', [
            'id' => $training->id,
            'title' => 'Training with File',
            'description' => 'This training has a file',
            'category' => 'File Category',
        ]);

        // Verify file was deleted
        $this->assertFalse(Storage::disk('public')->exists($filePath));
    }

    public function test_admin_can_delete_training_with_nonexistent_file(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        $training = Training::factory()->create([
            'title' => 'Training with Missing File',
            'file_path' => 'trainings/missing-file.pdf',
            'file_name' => 'missing-file.pdf',
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('trainings', [
            'id' => $training->id,
            'title' => 'Training with Missing File',
        ]);
    }

    public function test_unauthorized_user_cannot_delete_training(): void
    {
        $user = User::factory()->create();

        $training = Training::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'deleted_at' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_delete_training(): void
    {
        $training = Training::factory()->create();

        $response = $this->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(401);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'deleted_at' => null,
        ]);
    }

    public function test_delete_training_returns_404_for_nonexistent_training(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        $response = $this->actingAs($admin)
            ->deleteJson('/v1/admin/trainings/99999');

        $response->assertStatus(404);
    }

    public function test_delete_training_handles_database_errors_gracefully(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        $training = Training::factory()->create();

        $this->mock(\App\Services\TrainingService::class, function ($mock) {
            $mock->shouldReceive('deleteTraining')
                ->andThrow(new \Exception('Database connection failed'));
        });

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(500)
            ->assertJson([
                'message' => __('app.action.failed'),
            ]);
    }

    public function test_delete_training_returns_500_when_service_fails(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        $training = Training::factory()->create();

        $this->mock(\App\Services\TrainingService::class, function ($mock) use ($training) {
            $mock->shouldReceive('deleteTraining')
                ->once()
                ->with($training)
                ->andReturn(false);
        });

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(500)
            ->assertJson([
                'message' => __('app.action.failed'),
            ]);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'deleted_at' => null,
        ]);
    }

    public function test_delete_training_returns_correct_response_structure(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        $training = Training::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(204);
        $this->assertEmpty($response->getContent());
    }

    public function test_delete_training_with_different_file_types(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        // Test with PDF file
        $pdfPath = 'trainings/test.pdf';
        Storage::disk('public')->put($pdfPath, 'fake pdf content');

        $pdfTraining = Training::factory()->create([
            'title' => 'PDF Training',
            'file_path' => $pdfPath,
            'file_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$pdfTraining->id}");

        $response->assertStatus(204);
        $this->assertFalse(Storage::disk('public')->exists($pdfPath));

        // Test with PPTX file
        $pptxPath = 'trainings/test.pptx';
        Storage::disk('public')->put($pptxPath, 'fake pptx content');

        $pptxTraining = Training::factory()->create([
            'title' => 'PPTX Training',
            'file_path' => $pptxPath,
            'file_name' => 'test.pptx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$pptxTraining->id}");

        $response->assertStatus(204);
        $this->assertFalse(Storage::disk('public')->exists($pptxPath));
    }

    public function test_delete_training_preserves_other_trainings(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        $training1 = Training::factory()->create(['title' => 'Training 1']);
        $training2 = Training::factory()->create(['title' => 'Training 2']);
        $training3 = Training::factory()->create(['title' => 'Training 3']);

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$training2->id}");

        $response->assertStatus(204);

        // Verify only training2 was soft deleted
        $this->assertSoftDeleted('trainings', ['id' => $training2->id]);
        $this->assertDatabaseHas('trainings', [
            'id' => $training1->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('trainings', [
            'id' => $training3->id,
            'deleted_at' => null,
        ]);
    }

    public function test_delete_training_with_complex_file_structure(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('training.delete');

        // Create nested directory structure
        $nestedPath = 'trainings/2024/january/complex-training.pdf';
        Storage::disk('public')->put($nestedPath, 'fake nested file content');

        $training = Training::factory()->create([
            'title' => 'Complex Training',
            'file_path' => $nestedPath,
            'file_name' => 'complex-training.pdf',
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/v1/admin/trainings/{$training->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('trainings', [
            'id' => $training->id,
            'title' => 'Complex Training',
        ]);

        $this->assertFalse(Storage::disk('public')->exists($nestedPath));
    }
}
