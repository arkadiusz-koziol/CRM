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
            'material.list',
            'material.show',
            'material.create',
            'material.update',
            'material.delete',
            'city.create',
            'city.update',
            'city.delete',
            'city.list',
            'estate.create',
            'estate.update',
            'estate.delete',
            'estate.list',
            'estate.show',
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
