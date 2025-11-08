<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\UsableCountDto;
use App\Exceptions\UsableCountUnavailableException;
use App\Factory\UsableCountDtoFactory;
use App\Interfaces\Repositories\ToolRepositoryInterface;
use Psr\Log\LoggerInterface;

final class GetUsableToolCountService
{
    public function __construct(
        private readonly ToolRepositoryInterface $toolRepository,
        private readonly UsableCountDtoFactory $dtoFactory,
        private readonly LoggerInterface $logger
    ) {}

    public function handle(): UsableCountDto
    {
        try {
            $count = $this->toolRepository->countUsable();

            return $this->dtoFactory->create('tools', $count);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get usable tool count', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new UsableCountUnavailableException('tools', 0, $e);
        }
    }
}
