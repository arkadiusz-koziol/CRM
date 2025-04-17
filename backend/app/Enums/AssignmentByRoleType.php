<?php

namespace App\Enums;

use InvalidArgumentException;

enum AssignmentByRoleType: string
{
    case ALL = 'all';
    case ROLE = 'role';
    case USERS = 'users';

    public static function fromRequest(string $value): self {
        return match ($value) {
            'all' => self::ALL,
            'role' => self::ROLE,
            'users' => self::USERS,
            default => throw new InvalidArgumentException("Invalid assignment type"),
        };
    }
}
