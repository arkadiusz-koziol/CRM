<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Export;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ExportApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions
        $this->seed(PermissionSeeder::class);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['export.create', 'export.manage']);

        Sanctum::actingAs($this->user);

        Storage::fake('local');
    }

    public function test_export_csv_returns_file_download(): void
    {
        $data = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];

        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => $data,
            'filename' => 'test_export',
            'headers' => ['Name', 'Email'],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="test_export.csv"');

        $this->assertStringContainsString('Name,Email', $response->getContent());
        $this->assertStringContainsString('"John Doe",john@example.com', $response->getContent());
    }

    public function test_export_xlsx_returns_file_download(): void
    {
        $data = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
        ];

        $response = $this->postJson('/api/v1/admin/export/xlsx', [
            'data' => $data,
            'filename' => 'test_export',
            'headers' => ['Name', 'Email'],
            'options' => ['title' => 'Test Export'],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename="test_export.xlsx"');
    }

    public function test_export_pdf_returns_file_download(): void
    {
        $data = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
        ];

        $response = $this->postJson('/api/v1/admin/export/pdf', [
            'data' => $data,
            'filename' => 'test_export',
            'headers' => ['Name', 'Email'],
            'options' => ['title' => 'Test Export'],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="test_export.pdf"');
    }

    public function test_export_csv_requires_authentication(): void
    {
        // Create a new test case without authentication
        $this->refreshApplication();

        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => [['name' => 'John Doe']],
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_export_csv_requires_permission(): void
    {
        $this->user->revokePermissionTo('export.create');

        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => [['name' => 'John Doe']],
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_export_csv_validates_required_data(): void
    {
        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => 'not an array',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['data']);
    }

    public function test_export_csv_validates_data_is_array(): void
    {
        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => 'not an array',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['data']);
    }

    public function test_export_csv_validates_filename_length(): void
    {
        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => [['name' => 'John Doe']],
            'filename' => str_repeat('a', 256), // Too long
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['filename']);
    }

    public function test_export_csv_validates_headers_are_strings(): void
    {
        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => [['name' => 'John Doe']],
            'headers' => [123, 'valid'], // Mixed types
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['headers.0']);
    }

    public function test_export_chunked_validates_format(): void
    {
        $response = $this->postJson('/api/v1/admin/export/chunked', [
            'data' => [['name' => 'John Doe']],
            'filename' => 'test',
            'format' => 'invalid',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'attributes' => [
                    'error',
                    'messages' => [
                        'format',
                    ],
                ],
            ],
            'meta' => [
                'request_id',
            ],
        ]);
    }

    public function test_cleanup_requires_manage_permission(): void
    {
        $this->user->revokePermissionTo('export.manage');

        $response = $this->postJson('/api/v1/admin/export/cleanup');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_cleanup_returns_success_response(): void
    {
        $response = $this->postJson('/api/v1/admin/export/cleanup');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'attributes' => [
                    'message',
                    'deleted_files',
                ],
            ],
            'meta' => [
                'request_id',
            ],
        ]);
    }

    public function test_export_with_empty_data_returns_file(): void
    {
        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => [],
            'filename' => 'empty_export',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="empty_export.csv"');
    }

    public function test_export_with_special_characters_handles_encoding(): void
    {
        $data = [
            ['name' => 'José María', 'email' => 'josé@example.com'],
            ['name' => 'François', 'email' => 'françois@example.com'],
        ];

        $response = $this->postJson('/api/v1/admin/export/csv', [
            'data' => $data,
            'filename' => 'unicode_export',
            'headers' => ['Name', 'Email'],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('José María', $content);
        $this->assertStringContainsString('François', $content);
    }
}
