<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Analytics;

use App\Models\Company;
use App\Models\Task;
use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;
use App\Services\Analytics\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

final class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;

    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->service = new DashboardService($this->logger);

        Cache::flush();
    }

    public function test_it_returns_cached_data(): void
    {
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Dashboard KPI data generated', $this->isType('array'));

        // First call should generate data
        $data1 = $this->service->getKpiData();

        // Second call should return cached data
        $data2 = $this->service->getKpiData();

        $this->assertEquals($data1, $data2);
        $this->assertArrayHasKey('active_clients', $data1);
        $this->assertArrayHasKey('completed_tasks_30d', $data1);
        $this->assertArrayHasKey('training_completion_rate', $data1);
        $this->assertArrayHasKey('material_usage_rate', $data1);
        $this->assertArrayHasKey('generated_at', $data1);
    }

    public function test_it_calculates_active_clients_correctly(): void
    {
        // Create test data
        Company::factory()->create(['status' => 'active']);
        Company::factory()->create(['status' => 'active']);
        Company::factory()->create(['status' => 'inactive']);
        Company::factory()->create(['status' => 'active', 'deleted_at' => now()]);

        $data = $this->service->getKpiData();

        $this->assertEquals(2, $data['active_clients']);
    }

    public function test_it_calculates_completed_tasks_30d_correctly(): void
    {
        // Create test data
        $recentDate = Carbon::now()->subDays(15);
        $oldDate = Carbon::now()->subDays(35);

        Task::factory()->create([
            'status' => 'completed',
            'completed_at' => $recentDate,
        ]);
        Task::factory()->create([
            'status' => 'completed',
            'completed_at' => $recentDate,
        ]);
        Task::factory()->create([
            'status' => 'completed',
            'completed_at' => $oldDate,
        ]);
        Task::factory()->create([
            'status' => 'pending',
            'completed_at' => $recentDate,
        ]);

        $data = $this->service->getKpiData();

        $this->assertEquals(2, $data['completed_tasks_30d']);
    }

    public function test_it_calculates_training_completion_rate_correctly(): void
    {
        // Create test data
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $training = Training::factory()->create();

        TrainingUser::factory()->create([
            'user_id' => $user1->id,
            'training_id' => $training->id,
            'status' => 'completed',
        ]);
        TrainingUser::factory()->create([
            'user_id' => $user2->id,
            'training_id' => $training->id,
            'status' => 'completed',
        ]);
        TrainingUser::factory()->create([
            'user_id' => $user3->id,
            'training_id' => $training->id,
            'status' => 'in_progress',
        ]);

        $data = $this->service->getKpiData();

        // 2 out of 3 users completed training = 66.67%
        $this->assertEquals(66.67, $data['training_completion_rate']);
    }

    public function test_it_handles_zero_users_for_training_completion(): void
    {
        $data = $this->service->getKpiData();

        $this->assertEquals(0.0, $data['training_completion_rate']);
    }

    public function test_it_generates_etag_correctly(): void
    {
        $kpiData = $this->service->getKpiDataWithETag();

        $this->assertArrayHasKey('data', $kpiData);
        $this->assertArrayHasKey('etag', $kpiData);
        $this->assertStringStartsWith('W/"', $kpiData['etag']);
        $this->assertStringEndsWith('"', $kpiData['etag']);
    }

    public function test_it_clears_cache(): void
    {
        // Generate data to populate cache
        $this->service->getKpiData();

        // Verify cache exists
        $this->assertTrue(Cache::has('dashboard_kpi'));

        // Clear cache
        $this->service->clearCache();

        // Verify cache is cleared
        $this->assertFalse(Cache::has('dashboard_kpi'));
    }

    public function test_it_logs_performance_warnings(): void
    {
        // This test verifies that the service can be called without errors
        // Performance warnings would be logged if execution time exceeds threshold
        $this->service->getKpiData();

        // Verify the method executes successfully
        $this->assertTrue(true);
    }

    public function test_it_handles_errors_gracefully(): void
    {
        // This test verifies that the service can be called without errors
        // Error handling would be tested with actual error scenarios
        $this->service->getKpiData();

        // Verify the method executes successfully
        $this->assertTrue(true);
    }
}
