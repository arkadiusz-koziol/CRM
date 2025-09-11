<?php

namespace App\Services;

use App\Dto\ToolDto;
use App\Interfaces\Repositories\ToolRepositoryInterface;
use App\Models\Tool;

class ToolService
{
    public function __construct(
        protected ToolRepositoryInterface $toolRepository
    ) {}

    public function createTool(ToolDto $toolDTO): Tool
    {
        return $this->toolRepository->create($toolDTO);
    }

    public function updateTool(Tool $tool, ToolDto $toolDTO): bool
    {
        return $this->toolRepository->update($tool, $toolDTO);
    }

    public function deleteTool(Tool $tool): bool
    {
        return $this->toolRepository->delete($tool);
    }

    public function getToolById(int $id): ?Tool
    {
        return $this->toolRepository->findById($id);
    }

    public function getAllTools(): array
    {
        return $this->toolRepository->findAll();
    }

    public function getPaginatedTools(int $page = 1, int $limit = 10): array
    {
        return $this->toolRepository->findPaginated($page, $limit);
    }
}
