<?php

declare(strict_types=1);

namespace App\Domain\Crm\Entity;

use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

final readonly class Contact
{
    public function __construct(
        private string $id,
        private string $firstName,
        private string $lastName,
        private string $email,
        private ?string $phone,
        private LeadLevel $leadLevel,
        private ?string $ownerUserId,
        private string $source,
        private ContactStatus $status,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public static function create(
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone,
        LeadLevel $leadLevel,
        ?string $ownerUserId,
        string $source,
        ContactStatus $status
    ): self {
        $now = Carbon::now();

        return new self(
            id: Uuid::uuid7()->toString(),
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            phone: $phone,
            leadLevel: $leadLevel,
            ownerUserId: $ownerUserId,
            source: $source,
            status: $status,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function fullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function leadLevel(): LeadLevel
    {
        return $this->leadLevel;
    }

    public function ownerUserId(): ?string
    {
        return $this->ownerUserId;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function status(): ContactStatus
    {
        return $this->status;
    }

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }

    public function updatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?Carbon
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
