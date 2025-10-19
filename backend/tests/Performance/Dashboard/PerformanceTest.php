<?php

declare(strict_types=1);

namespace Tests\Performance\Dashboard;

use App\Models\Company;
use App\Models\Task;
use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;
use App\Services\Analytics\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

final class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;

    private const PERFORMANCE_THRESHOLD_MS = 300;

    private const LARGE_DATASET_SIZE = 10000;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DashboardService::class);
        Cache::flush();
    }

    public function test_dashboard_performance_with_large_dataset(): void
    {
        $this->markTestSkipped('Performance test - run manually with large dataset');

        // Create large dataset
        $this->createLargeDataset();

        $startTime = microtime(true);

        $data = $this->service->getKpiData();

        $executionTime = (microtime(true) - $startTime) * 1000;

        $this->assertLessThan(
            self::PERFORMANCE_THRESHOLD_MS,
            $executionTime,
            "Dashboard KPI generation took {$executionTime}ms, exceeding threshold of ".self::PERFORMANCE_THRESHOLD_MS.'ms'
        );

        $this->assertArrayHasKey('active_clients', $data);
        $this->assertArrayHasKey('completed_tasks_30d', $data);
        $this->assertArrayHasKey('training_completion_rate', $data);
        $this->assertArrayHasKey('material_usage_rate', $data);
    }

    public function test_dashboard_performance_with_cached_data(): void
    {
        // Create moderate dataset
        $this->createModerateDataset();

        // First call - should populate cache
        $startTime = microtime(true);
        $data1 = $this->service->getKpiData();
        $firstCallTime = (microtime(true) - $startTime) * 1000;

        // Second call - should use cache
        $startTime = microtime(true);
        $data2 = $this->service->getKpiData();
        $secondCallTime = (microtime(true) - $startTime) * 1000;

        // Cached call should be significantly faster
        $this->assertLessThan($firstCallTime, $secondCallTime);
        $this->assertEquals($data1, $data2);

        // Both calls should be under threshold
        $this->assertLessThan(self::PERFORMANCE_THRESHOLD_MS, $firstCallTime);
        $this->assertLessThan(self::PERFORMANCE_THRESHOLD_MS, $secondCallTime);
    }

    public function test_dashboard_performance_with_realistic_data(): void
    {
        // Create realistic dataset
        $this->createRealisticDataset();

        $startTime = microtime(true);

        $data = $this->service->getKpiData();

        $executionTime = (microtime(true) - $startTime) * 1000;

        $this->assertLessThan(
            self::PERFORMANCE_THRESHOLD_MS,
            $executionTime,
            "Dashboard KPI generation took {$executionTime}ms, exceeding threshold of ".self::PERFORMANCE_THRESHOLD_MS.'ms'
        );

        // Verify data integrity
        $this->assertIsInt($data['active_clients']);
        $this->assertIsInt($data['completed_tasks_30d']);
        $this->assertIsFloat($data['training_completion_rate']);
        $this->assertIsFloat($data['material_usage_rate']);
        $this->assertIsString($data['generated_at']);
    }

    public function test_etag_generation_performance(): void
    {
        $this->createModerateDataset();

        $startTime = microtime(true);

        $kpiData = $this->service->getKpiDataWithETag();

        $executionTime = (microtime(true) - $startTime) * 1000;

        $this->assertLessThan(
            self::PERFORMANCE_THRESHOLD_MS,
            $executionTime,
            "ETag generation took {$executionTime}ms, exceeding threshold of ".self::PERFORMANCE_THRESHOLD_MS.'ms'
        );

        $this->assertArrayHasKey('data', $kpiData);
        $this->assertArrayHasKey('etag', $kpiData);
        $this->assertStringStartsWith('W/"', $kpiData['etag']);
    }

    private function createLargeDataset(): void
    {
        // Create 10,000 companies
        Company::factory()->count(self::LARGE_DATASET_SIZE)->create(['status' => 'active']);

        // Create 5,000 users
        $users = User::factory()->count(5000)->create();

        // Create 20,000 tasks (mix of completed and pending)
        Task::factory()->count(15000)->create([
            'status' => 'completed',
            'completed_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);
        Task::factory()->count(5000)->create(['status' => 'pending']);

        // Create 100 trainings
        $trainings = Training::factory()->count(100)->create();

        // Create training user relationships
        foreach ($users as $user) {
            $randomTrainings = $trainings->random(rand(1, 5));
            foreach ($randomTrainings as $training) {
                TrainingUser::factory()->create([
                    'user_id' => $user->id,
                    'training_id' => $training->id,
                    'status' => rand(0, 1) ? 'completed' : 'in_progress',
                ]);
            }
        }
    }

    private function createModerateDataset(): void
    {
        // Create 1,000 companies
        Company::factory()->count(1000)->create(['status' => 'active']);

        // Create 500 users
        $users = User::factory()->count(500)->create();

        // Create 2,000 tasks
        Task::factory()->count(1500)->create([
            'status' => 'completed',
            'completed_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);
        Task::factory()->count(500)->create(['status' => 'pending']);

        // Create 50 trainings
        $trainings = Training::factory()->count(50)->create();

        // Create training user relationships
        foreach ($users as $user) {
            $randomTrainings = $trainings->random(rand(1, 3));
            foreach ($randomTrainings as $training) {
                TrainingUser::factory()->create([
                    'user_id' => $user->id,
                    'training_id' => $training->id,
                    'status' => rand(0, 1) ? 'completed' : 'in_progress',
                ]);
            }
        }
    }

    private function createRealisticDataset(): void
    {
        // Create 100 companies
        Company::factory()->count(100)->create(['status' => 'active']);
        Company::factory()->count(20)->create(['status' => 'inactive']);

        // Create 200 users
        $users = User::factory()->count(200)->create();

        // Create 500 tasks
        Task::factory()->count(300)->create([
            'status' => 'completed',
            'completed_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);
        Task::factory()->count(200)->create(['status' => 'pending']);

        // Create 20 trainings
        $trainings = Training::factory()->count(20)->create();

        // Create training user relationships
        foreach ($users as $user) {
            $randomTrainings = $trainings->random(rand(1, 5));
            foreach ($randomTrainings as $training) {
                TrainingUser::factory()->create([
                    'user_id' => $user->id,
                    'training_id' => $training->id,
                    'status' => rand(0, 1) ? 'completed' : 'in_progress',
                ]);
            }
        }
    }
}
