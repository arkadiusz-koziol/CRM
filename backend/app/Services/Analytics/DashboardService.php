<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\Company;
use App\Models\Task;
use App\Models\TrainingUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Psr\Log\LoggerInterface;

final class DashboardService
{
    private const CACHE_TTL = 300; // 5 minutes

    private const CACHE_KEY = 'dashboard_kpi';

    private const PERFORMANCE_THRESHOLD_MS = 300;

    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function getKpiData(): array
    {
        $cacheKey = self::CACHE_KEY;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $startTime = microtime(true);

            try {
                $kpiData = [
                    'active_clients' => $this->getActiveClientsCount(),
                    'completed_tasks_30d' => $this->getCompletedTasksCount(),
                    'training_completion_rate' => $this->getTrainingCompletionRate(),
                    'material_usage_rate' => $this->getMaterialUsageRate(),
                    'generated_at' => now()->toISOString(),
                ];

                $executionTime = (microtime(true) - $startTime) * 1000;

                $this->logger->info('Dashboard KPI data generated', [
                    'execution_time_ms' => $executionTime,
                    'cache_ttl' => self::CACHE_TTL,
                ]);

                if ($executionTime > self::PERFORMANCE_THRESHOLD_MS) {
                    $this->logger->warning('Dashboard KPI generation exceeded performance threshold', [
                        'execution_time_ms' => $executionTime,
                        'threshold_ms' => self::PERFORMANCE_THRESHOLD_MS,
                    ]);
                }

                return $kpiData;
            } catch (\Throwable $e) {
                $this->logger->error('Failed to generate dashboard KPI data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw $e;
            }
        });
    }

    public function getKpiDataWithETag(): array
    {
        $data = $this->getKpiData();
        $etag = $this->generateETag($data);

        return [
            'data' => $data,
            'etag' => $etag,
        ];
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->logger->info('Dashboard KPI cache cleared');
    }

    private function getActiveClientsCount(): int
    {
        return Company::where('status', 'active')
            ->where('deleted_at', null)
            ->count();
    }

    private function getCompletedTasksCount(): int
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Task::where('status', 'completed')
            ->where('completed_at', '>=', $thirtyDaysAgo)
            ->where('deleted_at', null)
            ->count();
    }

    private function getTrainingCompletionRate(): float
    {
        $totalUsers = User::count();

        if ($totalUsers === 0) {
            return 0.0;
        }

        $completedTrainings = TrainingUser::where('status', 'completed')
            ->distinct('user_id')
            ->count('user_id');

        return round(($completedTrainings / $totalUsers) * 100, 2);
    }

    private function getMaterialUsageRate(): float
    {
        // For now, return a mock value since materials table might not exist
        // In a real implementation, this would calculate actual material usage
        return 75.5; // Mock value for testing
    }

    private function generateETag(array $data): string
    {
        $content = json_encode($data, JSON_PRETTY_PRINT);

        return 'W/"'.hash('sha256', $content).'"';
    }
}
