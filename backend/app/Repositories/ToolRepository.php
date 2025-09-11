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

    public function findPaginated(int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;

        $tools = Tool::offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();

        $total = Tool::count();

        return [
            'data' => $tools,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => (int) ceil($total / $limit),
                'from' => $offset + 1,
                'to' => min($offset + $limit, $total),
            ]
        ];
    }
}
