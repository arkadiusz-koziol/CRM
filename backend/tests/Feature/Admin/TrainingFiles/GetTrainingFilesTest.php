<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\TrainingFiles;

use App\Models\Training;
use App\Models\TrainingFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

final class GetTrainingFilesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo('training.file.list');
    }

    public function test_admin_can_get_training_files(): void
    {
        $training = Training::factory()->create();
        $files = TrainingFile::factory()->count(3)->create(['training_id' => $training->id]);

        $response = $this->actingAs($this->admin)->getJson("/api/v1/admin/trainings/{$training->id}/files");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data')
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
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ]);
    }

    public function test_admin_gets_empty_list_if_no_files_attached(): void
    {
        $training = Training::factory()->create();

        $response = $this->actingAs($this->admin)->getJson("/api/v1/admin/trainings/{$training->id}/files");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(0, 'data');
    }

    public function test_admin_cannot_get_files_from_non_existent_training(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/trainings/99999/files');

        $response->assertNotFound()
            ->assertJson(['message' => __('app.training.not_found')]);
    }

    public function test_unauthenticated_user_cannot_get_training_files(): void
    {
        $training = Training::factory()->create();

        $response = $this->getJson("/api/v1/admin/trainings/{$training->id}/files");

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_get_training_files(): void
    {
        $user = User::factory()->create();
        $training = Training::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/v1/admin/trainings/{$training->id}/files");

        $response->assertForbidden();
    }
}
