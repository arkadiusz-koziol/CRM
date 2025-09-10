<?php

namespace App\Repositories;

use App\Dto\ToolDto;
use App\Interfaces\Repositories\ToolRepositoryInterface;
use App\Models\Tool;

class ToolRepository implements ToolRepositoryInterface
{
    public function create(ToolDto $toolDTO): Tool
    {
        return Tool::create([
            'name' => $toolDTO->getName(),
            'description' => $toolDTO->getDescription(),
            'count' => $toolDTO->getCount(),
        ]);
    }

    public function update(Tool $tool, ToolDto $toolDTO): bool
    {
        return $tool->update([
            'name' => $toolDTO->getName(),
            'description' => $toolDTO->getDescription(),
            'count' => $toolDTO->getCount(),
        ]);
    }

    public function delete(Tool $tool): bool
    {
        return $tool->delete();
    }

    public function findById(int $id): ?Tool
    {
        return Tool::find($id);
    }

    public function findAll(): array
    {
        return Tool::all()->toArray();
    }
}
