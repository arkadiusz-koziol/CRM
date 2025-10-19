<?php

declare(strict_types=1);

namespace App\Enums\Reports;

enum FilterOperator: string
{
    case EQUALS = 'eq';
    case NOT_EQUALS = 'ne';
    case GREATER_THAN = 'gt';
    case LESS_THAN = 'lt';
    case GREATER_THAN_OR_EQUAL = 'gte';
    case LESS_THAN_OR_EQUAL = 'lte';
    case IN = 'in';
    case NOT_IN = 'not_in';
    case LIKE = 'like';
    case BETWEEN = 'between';
    case IS_NULL = 'is_null';
    case IS_NOT_NULL = 'is_not_null';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::EQUALS => 'Equals',
            self::NOT_EQUALS => 'Not Equals',
            self::GREATER_THAN => 'Greater Than',
            self::LESS_THAN => 'Less Than',
            self::GREATER_THAN_OR_EQUAL => 'Greater Than or Equal',
            self::LESS_THAN_OR_EQUAL => 'Less Than or Equal',
            self::IN => 'In',
            self::NOT_IN => 'Not In',
            self::LIKE => 'Like',
            self::BETWEEN => 'Between',
            self::IS_NULL => 'Is Null',
            self::IS_NOT_NULL => 'Is Not Null',
        };
    }

    public function requiresValue(): bool
    {
        return ! in_array($this, [self::IS_NULL, self::IS_NOT_NULL], true);
    }

    public function requiresArrayValue(): bool
    {
        return in_array($this, [self::IN, self::NOT_IN, self::BETWEEN], true);
    }
}
