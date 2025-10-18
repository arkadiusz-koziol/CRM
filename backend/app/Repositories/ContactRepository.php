<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Crm\Entity\Contact as ContactEntity;
use App\Infrastructure\Contact\ContactMapper;
use App\Interfaces\Repositories\ContactRepositoryInterface;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;

class ContactRepository extends EloquentRepository implements ContactRepositoryInterface
{
    public function __construct(
        Contact $model,
        private readonly ContactMapper $mapper
    ) {
        parent::__construct($model);
    }

    public function findContactById(string $id): ?ContactEntity
    {
        $model = $this->model->find($id);
        return $model ? $this->mapToEntity($model) : null;
    }

    public function findByEmail(string $email): ?ContactEntity
    {
        $model = $this->model->where('email', $email)->first();
        return $model ? $this->mapToEntity($model) : null;
    }

    public function search(array $filters, int $perPage = 15, int $page = 1): array
    {
        $query = $this->model->newQuery();

        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['lead_level'])) {
            $query->where('lead_level', $filters['lead_level']);
        }

        if (isset($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (isset($filters['owner_user_id'])) {
            $query->where('owner_user_id', $filters['owner_user_id']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => collect($paginated->items())->map(fn($model) => $this->mapToEntity($model))->toArray(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ],
        ];
    }

    public function findByOwner(string $ownerId, array $filters = [], int $perPage = 15, int $page = 1): array
    {
        $filters['owner_user_id'] = $ownerId;
        return $this->search($filters, $perPage, $page);
    }

    public function save(ContactEntity $contact): void
    {
        $existingModel = $this->model->find($contact->id());

        if ($existingModel) {
            // Update existing record
            $existingModel->update([
                'first_name' => $contact->firstName(),
                'last_name' => $contact->lastName(),
                'email' => $contact->email(),
                'phone' => $contact->phone(),
                'lead_level' => $contact->leadLevel()->value,
                'owner_user_id' => $contact->ownerUserId(),
                'source' => $contact->source(),
                'status' => $contact->status()->value,
                'updated_at' => $contact->updatedAt(),
            ]);
        } else {
            // Create new record
            $model = $this->mapper->toModel($contact);
            $model->save();
        }
    }

    public function deleteContact(string $id): void
    {
        $this->model->find($id)?->delete();
    }

    public function restore(string $id): void
    {
        $this->model->withTrashed()->find($id)?->restore();
    }

    public function linkToCompany(string $contactId, string $companyId, string $position = null, bool $isPrimary = false): void
    {
        $contact = $this->model->find($contactId);
        if ($contact) {
            $contact->companies()->syncWithoutDetaching([
                $companyId => [
                    'position' => $position,
                    'is_primary' => $isPrimary,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    public function unlinkFromCompany(string $contactId, string $companyId): void
    {
        $contact = $this->model->find($contactId);
        if ($contact) {
            $contact->companies()->detach($companyId);
        }
    }

    public function getContactCompanies(string $contactId): array
    {
        $contact = $this->model->find($contactId);
        return $contact ? $contact->companies()->get()->toArray() : [];
    }

    public function bulkLinkToCompany(array $contactIds, string $companyId, string $position = null, bool $isPrimary = false): void
    {
        foreach ($contactIds as $contactId) {
            $this->linkToCompany($contactId, $companyId, $position, $isPrimary);
        }
    }

    public function bulkUnlinkFromCompany(array $contactIds, string $companyId): void
    {
        foreach ($contactIds as $contactId) {
            $this->unlinkFromCompany($contactId, $companyId);
        }
    }

    private function mapToEntity(Contact $model): ContactEntity
    {
        return $this->mapper->toDomain($model);
    }
}
