<?php

namespace App\Interfaces\Repositories;

use App\Dto\ToolDTO;
use App\Models\Tool;

interface ToolRepositoryInterface
{
    public function create(ToolDTO $toolDTO): Tool;
    public function update(Tool $tool, ToolDTO $toolDTO): bool;
    public function delete(Tool $tool): bool;
    public function findById(int $id): ?Tool;
    public function findAll(): array;
}
