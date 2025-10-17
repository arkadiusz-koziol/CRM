<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingFiles;

use App\Models\Training;
use App\Models\TrainingFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class DeleteTrainingFileTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->admin->givePermissionTo('training.file.delete');
    }

    public function test_admin_can_delete_training_file(): void
    {
        $training = Training::factory()->create();
        $file = TrainingFile::factory()->create(['training_id' => $training->id]);

        // Create a fake file in storage
        Storage::disk('public')->put($file->file_path, 'fake content');

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/{$training->id}/files/{$file->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => __('app.file.deleted_successfully')]);

        $this->assertDatabaseMissing('training_files', [
            'id' => $file->id,
        ]);

        Storage::disk('public')->assertMissing($file->file_path);
    }

    public function test_admin_cannot_delete_file_from_non_existent_training(): void
    {
        $file = TrainingFile::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/99999/files/{$file->id}");

        $response->assertNotFound()
            ->assertJson(['message' => __('app.training.not_found')]);
    }

    public function test_admin_cannot_delete_non_existent_file(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/{$training->id}/files/non-existent-id");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }

    public function test_admin_cannot_delete_file_from_different_training(): void
    {
        $training1 = Training::factory()->create();
        $training2 = Training::factory()->create();
        $file = TrainingFile::factory()->create(['training_id' => $training1->id]);

        $response = $this->actingAs($this->admin)->deleteJson("/v1/admin/trainings/{$training2->id}/files/{$file->id}");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => __('app.action.failed')]);
    }

    public function test_unauthenticated_user_cannot_delete_training_file(): void
    {
        $training = Training::factory()->create();
        $file = TrainingFile::factory()->create(['training_id' => $training->id]);

        $response = $this->deleteJson("/v1/admin/trainings/{$training->id}/files/{$file->id}");

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_delete_training_file(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $training = Training::factory()->create();
        $file = TrainingFile::factory()->create(['training_id' => $training->id]);

        $response = $this->actingAs($user)->deleteJson("/v1/admin/trainings/{$training->id}/files/{$file->id}");

        $response->assertForbidden();
    }
}
