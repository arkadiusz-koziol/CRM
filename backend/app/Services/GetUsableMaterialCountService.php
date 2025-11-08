<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\UsableCountDto;
use App\Exceptions\UsableCountUnavailableException;
use App\Factory\UsableCountDtoFactory;
use App\Interfaces\Repositories\MaterialRepositoryInterface;
use Psr\Log\LoggerInterface;

final class GetUsableMaterialCountService
{
    public function __construct(
        private readonly MaterialRepositoryInterface $materialRepository,
        private readonly UsableCountDtoFactory $dtoFactory,
        private readonly LoggerInterface $logger
    ) {}

    public function handle(): UsableCountDto
    {
        try {
            $count = $this->materialRepository->countUsable();

            return $this->dtoFactory->create('materials', $count);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get usable material count', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new UsableCountUnavailableException('materials', 0, $e);
        }
    }
}
