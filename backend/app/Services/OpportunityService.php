<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Crm\Entity\Opportunity;
use App\Events\OpportunityProbabilityChanged;
use App\Events\OpportunityStageChanged;
use App\Interfaces\Repositories\OpportunityRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

final class OpportunityService
{
    public function __construct(
        private readonly OpportunityRepositoryInterface $opportunityRepository,
        private readonly LoggerInterface $logger
    ) {}

    public function create(
        string $title,
        string $companyId,
        ?string $contactId,
        float $value,
        string $currency,
        int $probability,
        string $stageId,
        string $ownerUserId,
        ?string $closeDate = null
    ): string {
        $opportunity = Opportunity::create(
            title: $title,
            companyId: $companyId,
            contactId: $contactId,
            value: $value,
            currency: $currency,
            probability: $probability,
            stageId: $stageId,
            ownerUserId: $ownerUserId,
            closeDate: $closeDate ? \Carbon\Carbon::parse($closeDate) : null
        );

        $this->opportunityRepository->save($opportunity);

        $this->logger->info('Opportunity created', [
            'opportunity_id' => $opportunity->id(),
            'title' => $title,
            'company_id' => $companyId,
            'owner_user_id' => $ownerUserId,
        ]);

        return $opportunity->id();
    }

    public function findById(string $id): ?Opportunity
    {
        return $this->opportunityRepository->findById($id);
    }

    public function getKanbanData(): Collection
    {
        return $this->opportunityRepository->getKanbanData();
    }

    public function getFiltered(array $filters = []): Collection
    {
        return $this->opportunityRepository->getFiltered($filters);
    }

    public function updateStage(
        string $opportunityId,
        string $newStageId,
        User $user
    ): void {
        $opportunity = $this->opportunityRepository->findById($opportunityId);

        if (! $opportunity) {
            throw new \App\Exceptions\OpportunityNotFoundException("Opportunity with ID {$opportunityId} not found.");
        }

        $oldStageId = $opportunity->stageId();

        $this->opportunityRepository->updateStage($opportunityId, $newStageId);

        event(new OpportunityStageChanged(
            opportunityId: $opportunityId,
            oldStageId: $oldStageId,
            newStageId: $newStageId,
            userId: (string) $user->id,
            userName: $user->name,
            userEmail: $user->email
        ));

        $this->logger->info('Opportunity stage updated', [
            'opportunity_id' => $opportunityId,
            'old_stage_id' => $oldStageId,
            'new_stage_id' => $newStageId,
            'user_id' => $user->id,
        ]);
    }

    public function updateProbability(
        string $opportunityId,
        int $newProbability,
        User $user
    ): void {
        $opportunity = $this->opportunityRepository->findById($opportunityId);

        if (! $opportunity) {
            throw new \App\Exceptions\OpportunityNotFoundException("Opportunity with ID {$opportunityId} not found.");
        }

        $oldProbability = $opportunity->probability();

        $this->opportunityRepository->updateProbability($opportunityId, $newProbability);

        event(new OpportunityProbabilityChanged(
            opportunityId: $opportunityId,
            oldProbability: $oldProbability,
            newProbability: $newProbability,
            userId: (string) $user->id,
            userName: $user->name,
            userEmail: $user->email
        ));

        $this->logger->info('Opportunity probability updated', [
            'opportunity_id' => $opportunityId,
            'old_probability' => $oldProbability,
            'new_probability' => $newProbability,
            'user_id' => $user->id,
        ]);
    }

    public function update(
        string $id,
        ?string $title = null,
        ?string $companyId = null,
        ?string $contactId = null,
        ?float $value = null,
        ?string $currency = null,
        ?int $probability = null,
        ?string $stageId = null,
        ?string $ownerUserId = null,
        ?string $closeDate = null
    ): void {
        $opportunity = $this->opportunityRepository->findById($id);

        if (! $opportunity) {
            throw new \App\Exceptions\OpportunityNotFoundException("Opportunity with ID {$id} not found.");
        }

        // Create updated opportunity with new values
        $updatedOpportunity = new Opportunity(
            id: $opportunity->id(),
            title: $title ?? $opportunity->title(),
            companyId: $companyId ?? $opportunity->companyId(),
            contactId: $contactId ?? $opportunity->contactId(),
            value: $value ?? $opportunity->value(),
            currency: $currency ?? $opportunity->currency(),
            probability: $probability ?? $opportunity->probability(),
            stageId: $stageId ?? $opportunity->stageId(),
            ownerUserId: $ownerUserId ?? $opportunity->ownerUserId(),
            closeDate: $closeDate ? \Carbon\Carbon::parse($closeDate) : $opportunity->closeDate(),
            status: $opportunity->status(),
            createdAt: $opportunity->createdAt(),
            updatedAt: \Carbon\Carbon::now(),
            deletedAt: $opportunity->deletedAt()
        );

        $this->opportunityRepository->save($updatedOpportunity);

        $this->logger->info('Opportunity updated', [
            'opportunity_id' => $id,
            'updated_fields' => array_filter([
                'title' => $title,
                'company_id' => $companyId,
                'contact_id' => $contactId,
                'value' => $value,
                'currency' => $currency,
                'probability' => $probability,
                'stage_id' => $stageId,
                'owner_user_id' => $ownerUserId,
                'close_date' => $closeDate,
            ], fn ($value) => $value !== null),
        ]);
    }

    public function delete(string $id): void
    {
        $opportunity = $this->opportunityRepository->findById($id);

        if (! $opportunity) {
            throw new \App\Exceptions\OpportunityNotFoundException("Opportunity with ID {$id} not found.");
        }

        $this->opportunityRepository->delete($id);

        $this->logger->info('Opportunity deleted', [
            'opportunity_id' => $id,
        ]);
    }
}
