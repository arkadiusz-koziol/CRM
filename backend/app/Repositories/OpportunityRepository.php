<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Crm\Entity\Opportunity;
use App\Infrastructure\Opportunity\OpportunityMapper;
use App\Interfaces\Repositories\OpportunityRepositoryInterface;
use App\Models\Opportunity as OpportunityModel;
use Illuminate\Support\Collection;

final class OpportunityRepository extends EloquentRepository implements OpportunityRepositoryInterface
{
    protected string $model = OpportunityModel::class;

    public function __construct(
        private readonly OpportunityMapper $mapper
    ) {
        parent::__construct();
    }

    public function save(Opportunity $opportunity): void
    {
        $this->transactional(function () use ($opportunity): void {
            $model = $this->mapper->toModel($opportunity);
            $model->saveOrFail();
        });
    }

    public function findById(string $id): ?Opportunity
    {
        $model = $this->query()->find($id);

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findByCompanyId(string $companyId): Collection
    {
        return $this->query()
            ->where('company_id', $companyId)
            ->get()
            ->map(fn (OpportunityModel $model) => $this->mapper->toDomain($model));
    }

    public function findByOwnerId(string $ownerId): Collection
    {
        return $this->query()
            ->where('owner_user_id', $ownerId)
            ->get()
            ->map(fn (OpportunityModel $model) => $this->mapper->toDomain($model));
    }

    public function findByStageId(string $stageId): Collection
    {
        return $this->query()
            ->where('stage_id', $stageId)
            ->get()
            ->map(fn (OpportunityModel $model) => $this->mapper->toDomain($model));
    }

    public function findByStatus(string $status): Collection
    {
        return $this->query()
            ->where('status', $status)
            ->get()
            ->map(fn (OpportunityModel $model) => $this->mapper->toDomain($model));
    }

    public function getKanbanData(): Collection
    {
        return $this->query()
            ->with(['stage', 'company', 'contact', 'owner'])
            ->get()
            ->groupBy('stage_id')
            ->map(function (Collection $opportunities, string $stageId) {
                return [
                    'stage_id' => $stageId,
                    'stage_name' => $opportunities->first()->stage->name ?? 'Unknown',
                    'opportunities' => $opportunities->map(fn (OpportunityModel $model) => $this->mapper->toDomain($model)),
                ];
            });
    }

    public function updateStage(string $opportunityId, string $newStageId): void
    {
        $this->transactional(function () use ($opportunityId, $newStageId): void {
            $this->query()
                ->where('id', $opportunityId)
                ->update(['stage_id' => $newStageId]);
        });
    }

    public function updateProbability(string $opportunityId, int $probability): void
    {
        $this->transactional(function () use ($opportunityId, $probability): void {
            $this->query()
                ->where('id', $opportunityId)
                ->update(['probability' => $probability]);
        });
    }

    public function delete(string $id): void
    {
        $this->transactional(function () use ($id): void {
            $this->query()->where('id', $id)->delete();
        });
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->query();

        if (isset($filters['owner'])) {
            $query->where('owner_user_id', $filters['owner']);
        }

        if (isset($filters['company'])) {
            $query->where('company_id', $filters['company']);
        }

        if (isset($filters['stage'])) {
            $query->where('stage_id', $filters['stage']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('close_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('close_date', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%'.$filters['search'].'%')
                    ->orWhereHas('company', function ($companyQuery) use ($filters) {
                        $companyQuery->where('name', 'like', '%'.$filters['search'].'%');
                    });
            });
        }

        return $query->get()->map(fn (OpportunityModel $model) => $this->mapper->toDomain($model));
    }
}
