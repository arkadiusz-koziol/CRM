<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Crm\Entity\Opportunity;
use Illuminate\Support\Collection;

interface OpportunityRepositoryInterface
{
    public function save(Opportunity $opportunity): void;

    public function findById(string $id): ?Opportunity;

    public function findByCompanyId(string $companyId): Collection;

    public function findByOwnerId(string $ownerId): Collection;

    public function findByStageId(string $stageId): Collection;

    public function findByStatus(string $status): Collection;

    public function getKanbanData(): Collection;

    public function updateStage(string $opportunityId, string $newStageId): void;

    public function updateProbability(string $opportunityId, int $probability): void;

    public function delete(string $id): void;

    public function getFiltered(array $filters = []): Collection;
}
