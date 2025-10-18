<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Automation\WorkflowService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Psr\Log\LoggerInterface;

final class ProcessWorkflowRulesJob implements ShouldQueue
{
    use Queueable;

    private const LOCK_KEY = 'workflow_rules_processing';

    private const LOCK_TTL = 900; // 15 minutes

    public function __construct() {}

    public function handle(): void
    {
        $workflowService = app(WorkflowService::class);
        $logger = app(LoggerInterface::class);

        $lockKey = self::LOCK_KEY;

        if (Cache::has($lockKey)) {
            $logger->info('Workflow rules processing already in progress, skipping');

            return;
        }

        Cache::put($lockKey, true, self::LOCK_TTL);

        try {
            $logger->info('Starting workflow rules processing');

            $processed = $workflowService->processAllRules();

            $logger->info('Workflow rules processing completed', [
                'processed_count' => $processed,
            ]);
        } catch (\Throwable $e) {
            $logger->error('Workflow rules processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } finally {
            Cache::forget($lockKey);
        }
    }
}
