<?php

namespace App\Repositories;

use App\DTO\ToolDTO;
use App\Interfaces\Repositories\ToolRepositoryInterface;
use App\Models\Tool;

class ToolRepository implements ToolRepositoryInterface
{
    public function create(ToolDTO $toolDTO): Tool
    {
        return Tool::create([
            'name' => $toolDTO->name,
            'description' => $toolDTO->description,
            'count' => $toolDTO->count,
        ]);
    }

    public function update(Tool $tool, ToolDTO $toolDTO): bool
    {
        return $tool->update([
            'name' => $toolDTO->name,
            'description' => $toolDTO->description,
            'count' => $toolDTO->count,
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
