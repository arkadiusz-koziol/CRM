<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Crm\Entity\Contact;
use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use App\Interfaces\Repositories\ContactRepositoryInterface;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;

class ContactService
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
        private LoggerInterface $logger
    ) {}

    public function findById(string $id): ?Contact
    {
        return $this->contactRepository->findContactById($id);
    }

    public function search(array $filters, int $perPage = 15, int $page = 1): array
    {
        // Validate pagination limits
        $perPage = max(1, min(100, $perPage));
        $page = max(1, $page);

        return $this->contactRepository->search($filters, $perPage, $page);
    }

    public function findByOwner(string $ownerId, array $filters = [], int $perPage = 15, int $page = 1): array
    {
        // Validate pagination limits
        $perPage = max(1, min(100, $perPage));
        $page = max(1, $page);

        return $this->contactRepository->findByOwner($ownerId, $filters, $perPage, $page);
    }

    public function create(array $data, string $createdBy): Contact
    {
        // Check for duplicate email if provided
        if (isset($data['email']) && $data['email']) {
            $existingContact = $this->contactRepository->findByEmail($data['email']);
            if ($existingContact) {
                throw new \InvalidArgumentException('Contact with this email already exists.');
            }
        }

        $contact = Contact::create(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            leadLevel: LeadLevel::from($data['lead_level'] ?? LeadLevel::LEAD->value),
            ownerUserId: $data['owner_user_id'] ?? null,
            source: $data['source'],
            status: ContactStatus::from($data['status'] ?? ContactStatus::NEW->value)
        );

        $this->contactRepository->save($contact);

        $this->logger->info('Contact created', [
            'contact_id' => $contact->id(),
            'contact_name' => $contact->fullName(),
            'created_by' => $createdBy,
        ]);

        return $contact;
    }

    public function update(string $id, array $data): Contact
    {
        $contact = $this->contactRepository->findContactById($id);
        if (! $contact) {
            throw new \InvalidArgumentException('Contact not found.');
        }

        // Check for duplicate email if provided and different from current
        if (isset($data['email']) && $data['email'] && $data['email'] !== $contact->email()) {
            $existingContact = $this->contactRepository->findByEmail($data['email']);
            if ($existingContact && $existingContact->id() !== $contact->id()) {
                throw new \InvalidArgumentException('Contact with this email already exists.');
            }
        }

        // Create updated contact entity with same ID
        $updatedContact = new Contact(
            id: $contact->id(),
            firstName: $data['first_name'] ?? $contact->firstName(),
            lastName: $data['last_name'] ?? $contact->lastName(),
            email: $data['email'] ?? $contact->email(),
            phone: $data['phone'] ?? $contact->phone(),
            leadLevel: isset($data['lead_level']) ? LeadLevel::from($data['lead_level']) : $contact->leadLevel(),
            ownerUserId: $data['owner_user_id'] ?? $contact->ownerUserId(),
            source: $data['source'] ?? $contact->source(),
            status: isset($data['status']) ? ContactStatus::from($data['status']) : $contact->status(),
            createdAt: $contact->createdAt(),
            updatedAt: Carbon::now()
        );

        $this->contactRepository->save($updatedContact);

        $this->logger->info('Contact updated', [
            'contact_id' => $updatedContact->id(),
            'contact_name' => $updatedContact->fullName(),
        ]);

        return $updatedContact;
    }

    public function delete(string $id): void
    {
        $contact = $this->contactRepository->findContactById($id);
        if (! $contact) {
            throw new \InvalidArgumentException('Contact not found.');
        }

        $this->contactRepository->deleteContact($id);

        $this->logger->info('Contact deleted', [
            'contact_id' => $id,
            'contact_name' => $contact->fullName(),
        ]);
    }

    public function restore(string $id): void
    {
        $this->contactRepository->restore($id);

        $this->logger->info('Contact restored', [
            'contact_id' => $id,
        ]);
    }

    public function linkToCompany(string $contactId, string $companyId, ?string $position = null, bool $isPrimary = false): void
    {
        $contact = $this->contactRepository->findContactById($contactId);
        if (! $contact) {
            throw new \InvalidArgumentException('Contact not found.');
        }

        $this->contactRepository->linkToCompany($contactId, $companyId, $position, $isPrimary);

        $this->logger->info('Contact linked to company', [
            'contact_id' => $contactId,
            'company_id' => $companyId,
            'position' => $position,
            'is_primary' => $isPrimary,
        ]);
    }

    public function unlinkFromCompany(string $contactId, string $companyId): void
    {
        $contact = $this->contactRepository->findContactById($contactId);
        if (! $contact) {
            throw new \InvalidArgumentException('Contact not found.');
        }

        $this->contactRepository->unlinkFromCompany($contactId, $companyId);

        $this->logger->info('Contact unlinked from company', [
            'contact_id' => $contactId,
            'company_id' => $companyId,
        ]);
    }

    public function getContactCompanies(string $contactId): array
    {
        $contact = $this->contactRepository->findContactById($contactId);
        if (! $contact) {
            throw new \InvalidArgumentException('Contact not found.');
        }

        return $this->contactRepository->getContactCompanies($contactId);
    }

    public function bulkLinkToCompany(array $contactIds, string $companyId, ?string $position = null, bool $isPrimary = false): void
    {
        $this->contactRepository->bulkLinkToCompany($contactIds, $companyId, $position, $isPrimary);

        $this->logger->info('Contacts bulk linked to company', [
            'contact_ids' => $contactIds,
            'company_id' => $companyId,
            'position' => $position,
            'is_primary' => $isPrimary,
        ]);
    }

    public function bulkUnlinkFromCompany(array $contactIds, string $companyId): void
    {
        $this->contactRepository->bulkUnlinkFromCompany($contactIds, $companyId);

        $this->logger->info('Contacts bulk unlinked from company', [
            'contact_ids' => $contactIds,
            'company_id' => $companyId,
        ]);
    }
}
