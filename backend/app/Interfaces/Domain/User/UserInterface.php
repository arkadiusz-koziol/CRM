<?php

declare(strict_types=1);

namespace App\Interfaces\Domain\User;

use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;

interface UserInterface
{
    public function id(): UuidInterface;

    public function intId(): int;

    public function firstName(): string;

    public function lastName(): string;

    public function email(): string;

    public function username(): ?string;

    public function mentionNotificationsEnabled(): bool;

    public function mentionEmailNotificationsEnabled(): bool;

    public function createdAt(): Carbon;

    public function updatedAt(): Carbon;

    public function hasOptedOutOfMentions(): bool;

    public function hasOptedOutOfMentionEmails(): bool;
}
