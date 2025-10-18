<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Automation\Entity\Workflow;

interface WorkflowRepositoryInterface
{
    public function findById(string $id): ?Workflow;

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array;

    public function save(Workflow $workflow): void;

    public function update(Workflow $workflow): void;

    public function delete(string $id): void;
}
