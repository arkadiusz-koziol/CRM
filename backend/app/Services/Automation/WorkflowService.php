<?php

declare(strict_types=1);

namespace App\Services\Automation;

use App\Domain\Automation\Entity\Workflow;
use App\Domain\Automation\Entity\WorkflowRule;
use App\Interfaces\Repositories\WorkflowRepositoryInterface;
use App\Interfaces\Repositories\WorkflowRuleRepositoryInterface;
use Psr\Log\LoggerInterface;

final class WorkflowService
{
    public function __construct(
        private WorkflowRepositoryInterface $workflowRepository,
        private WorkflowRuleRepositoryInterface $workflowRuleRepository,
        private WorkflowConditionEvaluator $conditionEvaluator,
        private WorkflowActionExecutor $actionExecutor,
        private LoggerInterface $logger
    ) {}

    public function createWorkflow(string $name, string $description): string
    {
        $workflow = Workflow::create($name, $description);
        $this->workflowRepository->save($workflow);

        $this->logger->info('Workflow created', ['workflow_id' => $workflow->id()]);

        return $workflow->id();
    }

    public function createRule(
        string $workflowId,
        string $name,
        string $description,
        array $conditions,
        array $actions,
        int $priority = 0
    ): string {
        $rule = WorkflowRule::create(
            $workflowId,
            $name,
            $description,
            $conditions,
            $actions,
            $priority
        );

        $this->workflowRuleRepository->save($rule);

        $this->logger->info('Workflow rule created', [
            'rule_id' => $rule->id(),
            'workflow_id' => $workflowId,
        ]);

        return $rule->id();
    }

    public function processAllRules(): int
    {
        $rules = $this->workflowRuleRepository->findActiveRules();
        $processed = 0;

        foreach ($rules as $rule) {
            try {
                if ($this->evaluateRule($rule)) {
                    $this->executeRuleActions($rule);
                    $processed++;
                }
            } catch (\Throwable $e) {
                $this->logger->error('Failed to process workflow rule', [
                    'rule_id' => $rule->id(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $processed;
    }

    private function evaluateRule(WorkflowRule $rule): bool
    {
        return $this->conditionEvaluator->evaluate($rule->conditions());
    }

    private function executeRuleActions(WorkflowRule $rule): void
    {
        foreach ($rule->actions() as $action) {
            try {
                $this->actionExecutor->execute($action);
            } catch (\Throwable $e) {
                $this->logger->error('Failed to execute workflow action', [
                    'rule_id' => $rule->id(),
                    'action' => $action,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
