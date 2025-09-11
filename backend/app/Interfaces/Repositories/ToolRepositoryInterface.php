<?php

namespace App\Interfaces\Repositories;

use App\Dto\ToolDto;
use App\Models\Tool;

interface ToolRepositoryInterface
{
    public function create(ToolDto $toolDTO): Tool;
    public function update(Tool $tool, ToolDto $toolDTO): bool;
    public function delete(Tool $tool): bool;
    public function findById(int $id): ?Tool;
    public function findAll(): array;
    public function findPaginated(int $page = 1, int $limit = 10, string $search = ''): array;
}
