<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Automation\Entity\WorkflowRule;

interface WorkflowRuleRepositoryInterface
{
    public function findById(string $id): ?WorkflowRule;

    public function findByWorkflowId(string $workflowId): array;

    public function findActiveRules(): array;

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array;

    public function save(WorkflowRule $rule): void;

    public function update(WorkflowRule $rule): void;

    public function delete(string $id): void;
}
