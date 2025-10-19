<?php

declare(strict_types=1);

namespace App\Enums\Reports;

enum ReportSource: string
{
    case USERS = 'users';
    case COMPANIES = 'companies';
    case CONTACTS = 'contacts';
    case TASKS = 'tasks';
    case OPPORTUNITIES = 'opportunities';
    case INVOICES = 'invoices';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::USERS => 'Users',
            self::COMPANIES => 'Companies',
            self::CONTACTS => 'Contacts',
            self::TASKS => 'Tasks',
            self::OPPORTUNITIES => 'Opportunities',
            self::INVOICES => 'Invoices',
        };
    }

    public function getAllowedColumns(): array
    {
        return match ($this) {
            self::USERS => [
                'id', 'name', 'email', 'created_at', 'updated_at', 'last_login_at',
            ],
            self::COMPANIES => [
                'id', 'name', 'industry', 'source', 'status', 'region', 'vat_id', 'created_at', 'updated_at',
            ],
            self::CONTACTS => [
                'id', 'first_name', 'last_name', 'email', 'phone', 'lead_level', 'source', 'status', 'created_at', 'updated_at',
            ],
            self::TASKS => [
                'id', 'title', 'description', 'status', 'priority', 'due_date', 'completed_at', 'created_at', 'updated_at',
            ],
            self::OPPORTUNITIES => [
                'id', 'title', 'value', 'currency', 'probability', 'status', 'close_date', 'created_at', 'updated_at',
            ],
            self::INVOICES => [
                'id', 'number', 'issue_date', 'due_date', 'amount', 'currency', 'status', 'created_at', 'updated_at',
            ],
        };
    }
}
