<?php

use App\Enums\UserRoles;

return [
    'acl' => [
        UserRoles::ADMIN->value => [],

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
