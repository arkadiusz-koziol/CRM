<?php

declare(strict_types=1);

return [
    'workflow' => [
        'processing_interval' => 15, // minutes
        'chunk_size' => 100,
        'max_execution_time' => 300, // seconds
        'lock_ttl' => 900, // 15 minutes
    ],

    'conditions' => [
        'allowed_operators' => [
            'eq', 'ne', 'gt', 'lt', 'gte', 'lte',
            'in', 'not_in', 'between', 'exists', 'not_exists',
        ],
        'allowed_fields' => [
            'user.last_login_at',
            'user.created_at',
            'user.status',
            'company.status',
            'company.created_at',
            'opportunity.stage_id',
            'opportunity.value',
            'opportunity.probability',
            'opportunity.close_date',
            'task.status',
            'task.due_date',
            'task.completed_at',
        ],
    ],

    'actions' => [
        'create_task' => [
            'required_config' => ['title'],
            'optional_config' => ['description', 'assignee_id', 'due_date', 'priority'],
        ],
        'send_email' => [
            'required_config' => ['to', 'subject'],
            'optional_config' => ['body', 'template', 'variables'],
        ],
        'send_sms' => [
            'required_config' => ['to', 'message'],
            'optional_config' => ['template', 'variables'],
        ],
        'add_tag' => [
            'required_config' => ['entity_type', 'entity_id', 'tag'],
            'optional_config' => ['color', 'description'],
        ],
        'assign_owner' => [
            'required_config' => ['entity_type', 'entity_id', 'owner_id'],
            'optional_config' => ['role', 'notify'],
        ],
    ],
];
