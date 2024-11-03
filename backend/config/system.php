<?php

use App\Enums\UserRoles;

return [
    'acl' => [
        UserRoles::ADMIN->value => [
            'user.create',
            'user.show',
            'user.update',
            'user.delete',
            'user.list',
            'tool.list',
            'tool.show',
            'tool.create',
            'tool.update',
            'tool.delete',
        ],

        UserRoles::USER->value => [

        ],

        UserRoles::TECHNICIAN->value => [

        ],

        UserRoles::DRIVER->value => [

        ],

        UserRoles::OBJECT_MANAGER->value => [

        ],
    ],
];
