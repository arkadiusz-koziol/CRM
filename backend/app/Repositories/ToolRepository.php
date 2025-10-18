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

    public function findPaginated(int $page = 1, int $limit = 10, string $search = ''): array
    {
        $offset = ($page - 1) * $limit;

        $query = Tool::query();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();

        $tools = $query->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();

        $lastPage = (int) ceil($total / $limit);
        $isValidPage = $page <= $lastPage && $page > 0;

        return [
            'data' => $tools,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $isValidPage && $total > 0 ? $offset + 1 : 0,
                'to' => $isValidPage && $total > 0 ? min($offset + $limit, $total) : 0,
            ],
        ];
    }
}
