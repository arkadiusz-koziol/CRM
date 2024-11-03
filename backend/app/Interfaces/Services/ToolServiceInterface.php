<?php

namespace App\Interfaces\Services;

use App\DTO\ToolDTO;
use App\Models\Tool;

interface ToolServiceInterface
{
    public function createTool(ToolDTO $toolDTO): Tool;
    public function updateTool(Tool $tool, ToolDTO $toolDTO): bool;
    public function deleteTool(Tool $tool): bool;
    public function getToolById(int $id): ?Tool;
    public function getAllTools(): array;
}
