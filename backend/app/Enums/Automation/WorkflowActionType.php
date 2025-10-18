<?php

declare(strict_types=1);

namespace App\Enums\Automation;

enum WorkflowActionType: string
{
    case CREATE_TASK = 'create_task';
    case SEND_EMAIL = 'send_email';
    case SEND_SMS = 'send_sms';
    case ADD_TAG = 'add_tag';
    case ASSIGN_OWNER = 'assign_owner';

    public function label(): string
    {
        return match ($this) {
            self::CREATE_TASK => 'Create Task',
            self::SEND_EMAIL => 'Send Email',
            self::SEND_SMS => 'Send SMS',
            self::ADD_TAG => 'Add Tag',
            self::ASSIGN_OWNER => 'Assign Owner',
        };
    }
}
