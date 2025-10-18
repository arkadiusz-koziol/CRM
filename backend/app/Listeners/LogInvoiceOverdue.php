<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\InvoiceOverdue;
use App\Services\ActivityService;
use Psr\Log\LoggerInterface;

final class LogInvoiceOverdue
{
    public function __construct(
        private ActivityService $activityService,
        private LoggerInterface $logger
    ) {}

    public function handle(InvoiceOverdue $event): void
    {
        try {
            $this->activityService->log(
                'invoice.overdue',
                'Invoice overdue',
                [
                    'invoice_id' => $event->invoiceId,
                    'event' => 'invoice_overdue',
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to log invoice overdue', [
                'invoice_id' => $event->invoiceId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
