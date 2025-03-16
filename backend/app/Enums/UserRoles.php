<?php

namespace App\Enums;

enum UserRoles: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case TECHNICIAN = 'technician';
    case DRIVER = 'driver';
    case OBJECT_MANAGER = 'object_manager';

    public static function allowedForWeb(): array
    {
        return [
            self::ADMIN->value,
        ];
    }

    public static function allowedForApi(): array
    {
        return [
            self::ADMIN->value,
            self::USER->value,
            self::TECHNICIAN->value,
            self::DRIVER->value,
        ];
    }
}
