<?php

declare(strict_types=1);

return [
    'realtime' => [
        'enabled' => env('REALTIME_NOTIFICATIONS_ENABLED', true),
        'driver' => env('BROADCAST_DRIVER', 'redis'),
        'rate_limit' => [
            'attempts' => env('NOTIFICATION_RATE_LIMIT_ATTEMPTS', 100),
            'decay_minutes' => env('NOTIFICATION_RATE_LIMIT_DECAY_MINUTES', 1),
        ],
        'channels' => [
            'user' => 'user.{userId}',
            'entity' => 'entity.{entityType}.{entityId}',
            'team' => 'team.{teamId}',
            'admin' => 'admin',
        ],
        'events' => [
            'task.assigned' => [
                'channels' => ['user'],
                'rate_limited' => true,
            ],
            'comment.added' => [
                'channels' => ['entity', 'user'],
                'rate_limited' => true,
            ],
            'status.changed' => [
                'channels' => ['entity', 'user'],
                'rate_limited' => true,
            ],
            'opportunity.stage.changed' => [
                'channels' => ['entity', 'user'],
                'rate_limited' => true,
            ],
        ],
        'reconnect' => [
            'strategy' => 'exponential_backoff',
            'max_attempts' => 5,
            'base_delay' => 1000, // milliseconds
            'max_delay' => 30000, // milliseconds
        ],
    ],
];
