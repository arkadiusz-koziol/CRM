<?php

declare(strict_types=1);

namespace App\Infrastructure\Reports;

use App\Domain\Reports\Entity\ReportRun;
use App\Models\ReportRun as ReportRunModel;
use Carbon\Carbon;

final class ReportRunMapper
{
    public function toDomain(ReportRunModel $model): ReportRun
    {
        return ReportRun::reconstitute(
            $model->id,
            $model->report_id,
            $model->run_by,
            $model->status->value,
            $model->parameters,
            $model->file_path,
            $model->file_name,
            $model->file_size,
            $model->mime_type,
            $model->started_at ? Carbon::parse($model->started_at) : null,
            $model->completed_at ? Carbon::parse($model->completed_at) : null,
            $model->error_message,
            Carbon::parse($model->created_at),
            Carbon::parse($model->updated_at),
        );
    }

    public function toModel(ReportRun $reportRun): ReportRunModel
    {
        $model = new ReportRunModel;
        $model->id = $reportRun->id();
        $model->report_id = $reportRun->reportId();
        $model->run_by = $reportRun->runBy();
        $model->status = $reportRun->status();
        $model->parameters = $reportRun->parameters();
        $model->file_path = $reportRun->filePath();
        $model->file_name = $reportRun->fileName();
        $model->file_size = $reportRun->fileSize();
        $model->mime_type = $reportRun->mimeType();
        $model->started_at = $reportRun->startedAt();
        $model->completed_at = $reportRun->completedAt();
        $model->error_message = $reportRun->errorMessage();
        $model->created_at = $reportRun->createdAt();
        $model->updated_at = $reportRun->updatedAt();

        return $model;
    }
}
