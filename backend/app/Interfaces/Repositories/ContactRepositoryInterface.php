<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Crm\Entity\Contact;
use App\Interfaces\Repositories\Base\RepositoryInterface;

interface ContactRepositoryInterface extends RepositoryInterface
{
    public function findContactById(string $id): ?Contact;

    public function findByEmail(string $email): ?Contact;

    public function search(array $filters, int $perPage = 15, int $page = 1): array;

    public function findByOwner(string $ownerId, array $filters = [], int $perPage = 15, int $page = 1): array;

    public function save(Contact $contact): void;

    public function deleteContact(string $id): void;

    public function restore(string $id): void;

    public function linkToCompany(string $contactId, string $companyId, ?string $position = null, bool $isPrimary = false): void;

    public function unlinkFromCompany(string $contactId, string $companyId): void;

    public function getContactCompanies(string $contactId): array;

    public function bulkLinkToCompany(array $contactIds, string $companyId, ?string $position = null, bool $isPrimary = false): void;

    public function bulkUnlinkFromCompany(array $contactIds, string $companyId): void;
}
