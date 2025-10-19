<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Interfaces\Domain\User\UserInterface;
use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;

final class User implements UserInterface
{
    public function __construct(
        private readonly UuidInterface $id,
        private readonly int $intId,
        private readonly string $name,
        private readonly string $surname,
        private readonly string $email,
        private readonly ?string $username,
        private readonly bool $mentionNotificationsEnabled,
        private readonly bool $mentionEmailNotificationsEnabled,
        private readonly Carbon $createdAt,
        private readonly Carbon $updatedAt,
    ) {}

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function intId(): int
    {
        return $this->intId;
    }

    public function firstName(): string
    {
        return $this->name;
    }

    public function lastName(): string
    {
        return $this->surname;
    }

    public function fullName(): string
    {
        return $this->name.' '.$this->surname;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function mentionNotificationsEnabled(): bool
    {
        return $this->mentionNotificationsEnabled;
    }

    public function mentionEmailNotificationsEnabled(): bool
    {
        return $this->mentionEmailNotificationsEnabled;
    }

    public function hasOptedOutOfMentions(): bool
    {
        return ! $this->mentionNotificationsEnabled;
    }

    public function hasOptedOutOfMentionEmails(): bool
    {
        return ! $this->mentionEmailNotificationsEnabled;
    }

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }

    public function updatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public static function create(
        UuidInterface $id,
        int $intId,
        string $name,
        string $surname,
        string $email,
        ?string $username = null,
        bool $mentionNotificationsEnabled = true,
        bool $mentionEmailNotificationsEnabled = true,
    ): self {
        $now = Carbon::now();

        return new self(
            $id,
            $intId,
            $name,
            $surname,
            $email,
            $username,
            $mentionNotificationsEnabled,
            $mentionEmailNotificationsEnabled,
            $now,
            $now,
        );
    }
}
