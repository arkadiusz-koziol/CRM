<?php

declare(strict_types=1);

namespace App\Services\Automation;

final class WorkflowConditionEvaluator
{
    private const ALLOWED_OPERATORS = [
        'eq', 'ne', 'gt', 'lt', 'gte', 'lte', 'in', 'not_in', 'between', 'exists', 'not_exists',
    ];

    public function evaluate(array $conditions): bool
    {
        if (empty($conditions)) {
            return false;
        }

        return $this->evaluateConditionGroup($conditions);
    }

    private function evaluateConditionGroup(array $conditions): bool
    {
        $operator = $conditions['operator'] ?? 'and';
        $conditionsList = $conditions['conditions'] ?? [];

        if (empty($conditionsList)) {
            return false;
        }

        $results = [];
        foreach ($conditionsList as $condition) {
            $results[] = $this->evaluateSingleCondition($condition);
        }

        return match ($operator) {
            'and' => ! in_array(false, $results, true),
            'or' => in_array(true, $results, true),
            default => false
        };
    }

    private function evaluateSingleCondition(array $condition): bool
    {
        $field = $condition['field'] ?? '';
        $operator = $condition['operator'] ?? '';
        $value = $condition['value'] ?? null;

        if (! in_array($operator, self::ALLOWED_OPERATORS, true)) {
            return false;
        }

        // This is a simplified implementation
        // In a real system, you would fetch the actual data based on the field
        // For now, we'll return a random result for demonstration
        return $this->evaluateCondition($field, $operator, $value);
    }

    private function evaluateCondition(string $field, string $operator, mixed $value): bool
    {
        // Simplified evaluation logic
        // In a real implementation, you would:
        // 1. Parse the field to determine the model and attribute
        // 2. Query the database for the actual data
        // 3. Compare the actual data with the condition value

        return match ($operator) {
            'eq' => $this->getFieldValue($field) === $value,
            'ne' => $this->getFieldValue($field) !== $value,
            'gt' => $this->getFieldValue($field) > $value,
            'lt' => $this->getFieldValue($field) < $value,
            'gte' => $this->getFieldValue($field) >= $value,
            'lte' => $this->getFieldValue($field) <= $value,
            'in' => in_array($this->getFieldValue($field), (array) $value, true),
            'not_in' => ! in_array($this->getFieldValue($field), (array) $value, true),
            'exists' => $this->getFieldValue($field) !== null,
            'not_exists' => $this->getFieldValue($field) === null,
            'between' => $this->isBetween($this->getFieldValue($field), $value),
            default => false
        };
    }

    private function getFieldValue(string $field): mixed
    {
        // Simplified implementation - in reality you would query the database
        // based on the field path (e.g., "user.last_login_at", "company.status")
        return match ($field) {
            'user.last_login_at' => \Carbon\Carbon::now()->subDays(rand(1, 60)),
            'company.status' => ['active', 'inactive', 'prospect'][rand(0, 2)],
            'opportunity.stage_id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            default => null
        };
    }

    private function isBetween(mixed $value, array $range): bool
    {
        if (! is_numeric($value) || count($range) !== 2) {
            return false;
        }

        [$min, $max] = $range;

        return $value >= $min && $value <= $max;
    }
}
