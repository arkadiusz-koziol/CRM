<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Billing\Entity\Contract;
use App\Dto\CreateContractDto;
use App\Dto\UpdateContractDto;
use App\Enums\Billing\ContractStatus;
use App\Events\ContractActivated;
use App\Interfaces\Repositories\ContractRepositoryInterface;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;

final class ContractService
{
    public function __construct(
        private ContractRepositoryInterface $contractRepository,
        private LoggerInterface $logger
    ) {}

    public function create(CreateContractDto $dto): string
    {
        $contract = Contract::create(
            nr: $dto->number(),
            companyId: $dto->companyId(),
            startAt: $dto->startAt(),
            endAt: $dto->endAt(),
            amount: $dto->amount(),
            currency: $dto->currency(),
            status: ContractStatus::from($dto->status())
        );

        $this->contractRepository->save($contract);

        if ($contract->status() === ContractStatus::ACTIVE) {
            event(new ContractActivated($contract->id()));
        }

        return $contract->id();
    }

    public function update(UpdateContractDto $dto): string
    {
        $contract = $this->contractRepository->findById($dto->id());

        if (! $contract) {
            throw new \RuntimeException("Contract with ID {$dto->id()} not found");
        }

        $updatedContract = new Contract(
            id: $contract->id(),
            nr: $dto->number() ?? $contract->nr(),
            companyId: $dto->companyId() ?? $contract->companyId(),
            startAt: $dto->startAt() ?? $contract->startAt(),
            endAt: $dto->endAt() ?? $contract->endAt(),
            amount: $dto->amount() ?? $contract->amount(),
            currency: $dto->currency() ?? $contract->currency(),
            status: $dto->status() ? ContractStatus::from($dto->status()) : $contract->status(),
            createdAt: $contract->createdAt(),
            updatedAt: Carbon::now(),
            deletedAt: $contract->deletedAt()
        );

        $this->contractRepository->update($updatedContract);

        if ($updatedContract->status() === ContractStatus::ACTIVE && $contract->status() !== ContractStatus::ACTIVE) {
            event(new ContractActivated($updatedContract->id()));
        }

        return $updatedContract->id();
    }

    public function findById(string $id): ?Contract
    {
        return $this->contractRepository->findById($id);
    }

    public function findByNumber(string $number): ?Contract
    {
        return $this->contractRepository->findByNumber($number);
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->contractRepository->findAll($filters, $limit, $offset);
    }

    public function delete(string $id): void
    {
        $contract = $this->contractRepository->findById($id);

        if (! $contract) {
            throw new \RuntimeException("Contract with ID {$id} not found");
        }

        $this->contractRepository->delete($id);
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        $validStatuses = array_column(ContractStatus::cases(), 'value');

        if (! in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        return $this->contractRepository->bulkUpdateStatus($ids, $status);
    }

    public function findByCompanyId(string $companyId): array
    {
        return $this->contractRepository->findByCompanyId($companyId);
    }

    public function findExpired(): array
    {
        return $this->contractRepository->findExpired();
    }

    public function findExpiring(int $days = 30): array
    {
        return $this->contractRepository->findExpiring($days);
    }
}
