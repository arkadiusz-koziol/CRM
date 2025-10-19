<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Reports\Entity\ReportRun;
use App\Interfaces\Repositories\ReportRunRepositoryInterface;
use App\Services\Reports\ReportGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class RunReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $reportRunId
    ) {}

    public function handle(
        ReportRunRepositoryInterface $reportRunRepository,
        ReportGeneratorService $reportGeneratorService
    ): void {
        $reportRun = $reportRunRepository->findById($this->reportRunId);

        if (! $reportRun) {
            Log::error('Report run not found', ['report_run_id' => $this->reportRunId]);

            return;
        }

        if (! $reportRun->isPending()) {
            Log::warning('Report run is not pending', [
                'report_run_id' => $this->reportRunId,
                'status' => $reportRun->status(),
            ]);

            return;
        }

        try {
            $reportRun->start();
            $reportRunRepository->save($reportRun);

            Log::info('Started report generation', [
                'report_run_id' => $this->reportRunId,
                'report_id' => $reportRun->reportId(),
            ]);

            $filePath = $reportGeneratorService->generateReport($reportRun);

            $reportRun->complete(
                $filePath,
                $this->generateFileName($reportRun),
                Storage::size($filePath),
                'text/csv'
            );

            $reportRunRepository->save($reportRun);

            Log::info('Report generation completed', [
                'report_run_id' => $this->reportRunId,
                'file_path' => $filePath,
            ]);
        } catch (\Throwable $e) {
            $reportRun->fail($e->getMessage());
            $reportRunRepository->save($reportRun);

            Log::error('Report generation failed', [
                'report_run_id' => $this->reportRunId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    private function generateFileName(ReportRun $reportRun): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "report_{$reportRun->reportId()}_{$timestamp}.csv";
    }
}
