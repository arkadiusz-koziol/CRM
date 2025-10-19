<?php

declare(strict_types=1);

namespace App\Enums\Reports;

enum ReportRunStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RUNNING => 'Running',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function isFinished(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED], true);
    }

    public function isInProgress(): bool
    {
        return in_array($this, [self::PENDING, self::RUNNING], true);
    }
}
