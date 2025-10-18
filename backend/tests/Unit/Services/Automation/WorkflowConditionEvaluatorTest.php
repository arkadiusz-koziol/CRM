<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Automation;

use App\Services\Automation\WorkflowConditionEvaluator;
use PHPUnit\Framework\TestCase;

final class WorkflowConditionEvaluatorTest extends TestCase
{
    private WorkflowConditionEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new WorkflowConditionEvaluator;
    }

    public function test_it_returns_false_for_empty_conditions(): void
    {
        $result = $this->evaluator->evaluate([]);

        $this->assertFalse($result);
    }

    public function test_it_evaluates_simple_condition(): void
    {
        $conditions = [
            'operator' => 'and',
            'conditions' => [
                [
                    'field' => 'user.last_login_at',
                    'operator' => 'eq',
                    'value' => '2024-01-01',
                ],
            ],
        ];

        $result = $this->evaluator->evaluate($conditions);

        $this->assertIsBool($result);
    }

    public function test_it_evaluates_and_operator(): void
    {
        $conditions = [
            'operator' => 'and',
            'conditions' => [
                [
                    'field' => 'user.status',
                    'operator' => 'eq',
                    'value' => 'active',
                ],
                [
                    'field' => 'user.last_login_at',
                    'operator' => 'lt',
                    'value' => '30 days ago',
                ],
            ],
        ];

        $result = $this->evaluator->evaluate($conditions);

        $this->assertIsBool($result);
    }

    public function test_it_evaluates_or_operator(): void
    {
        $conditions = [
            'operator' => 'or',
            'conditions' => [
                [
                    'field' => 'company.status',
                    'operator' => 'eq',
                    'value' => 'prospect',
                ],
                [
                    'field' => 'company.status',
                    'operator' => 'eq',
                    'value' => 'active',
                ],
            ],
        ];

        $result = $this->evaluator->evaluate($conditions);

        $this->assertIsBool($result);
    }

    public function test_it_handles_unknown_operator(): void
    {
        $conditions = [
            'operator' => 'unknown',
            'conditions' => [
                [
                    'field' => 'user.status',
                    'operator' => 'eq',
                    'value' => 'active',
                ],
            ],
        ];

        $result = $this->evaluator->evaluate($conditions);

        $this->assertFalse($result);
    }

    public function test_it_handles_unknown_field_operator(): void
    {
        $conditions = [
            'operator' => 'and',
            'conditions' => [
                [
                    'field' => 'user.status',
                    'operator' => 'unknown_operator',
                    'value' => 'active',
                ],
            ],
        ];

        $result = $this->evaluator->evaluate($conditions);

        $this->assertFalse($result);
    }
}
