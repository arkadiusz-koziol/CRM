<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessWorkflowRulesJob;
use Illuminate\Console\Command;

final class ScheduleWorkflowRulesCommand extends Command
{
    protected $signature = 'workflow:process-rules';

    protected $description = 'Process workflow rules and execute actions';

    public function handle(): int
    {
        $this->info('Starting workflow rules processing...');

        try {
            ProcessWorkflowRulesJob::dispatch();
            $this->info('Workflow rules processing job dispatched successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to dispatch workflow rules processing job: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
