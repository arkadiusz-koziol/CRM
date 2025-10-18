<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Services\ActivityService;
use Psr\Log\LoggerInterface;

final class LogInvoicePaid
{
    public function __construct(
        private ActivityService $activityService,
        private LoggerInterface $logger
    ) {}

    public function handle(InvoicePaid $event): void
    {
        try {
            $this->activityService->log(
                'invoice.paid',
                'Invoice paid',
                [
                    'invoice_id' => $event->invoiceId,
                    'event' => 'invoice_paid',
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to log invoice payment', [
                'invoice_id' => $event->invoiceId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
