<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ContractActivated;
use App\Services\ActivityService;
use Psr\Log\LoggerInterface;

final class LogContractActivated
{
    public function __construct(
        private ActivityService $activityService,
        private LoggerInterface $logger
    ) {}

    public function handle(ContractActivated $event): void
    {
        try {
            $this->activityService->log(
                'contract.activated',
                'Contract activated',
                [
                    'contract_id' => $event->contractId,
                    'event' => 'contract_activated',
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to log contract activation', [
                'contract_id' => $event->contractId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
