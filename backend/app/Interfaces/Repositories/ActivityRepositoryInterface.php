<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Activity\Entity\Activity;
use Illuminate\Support\Collection;

interface ActivityRepositoryInterface
{
    public function save(Activity $activity): void;

    public function getRecent(int $limit = 10): Collection;

    public function getByEntityType(string $entityType, int $limit = 10): Collection;
}
