<?php

namespace App\Services;

use App\Dto\ToolDTO;
use App\Interfaces\Repositories\ToolRepositoryInterface;
use App\Models\Tool;

class ToolService
{
    public function __construct(
        protected ToolRepositoryInterface $toolRepository
    ) {
    }

    public function createTool(ToolDTO $toolDTO): Tool
    {
        return $this->toolRepository->create($toolDTO);
    }

    public function updateTool(Tool $tool, ToolDTO $toolDTO): bool
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
}
