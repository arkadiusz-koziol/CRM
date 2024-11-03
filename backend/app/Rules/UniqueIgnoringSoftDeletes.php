<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\DatabaseManager;

class UniqueIgnoringSoftDeletes implements Rule
{
    protected DatabaseManager $dbManager;

    public function __construct(
        protected string $table,
        protected string $column = 'name'
    ) {
        $this->dbManager = app(DatabaseManager::class);
    }

    public function passes($attribute, $value): bool
    {
        $count = $this->dbManager->table($this->table)
            ->where($this->column, $value)
            ->whereNull('deleted_at')
            ->count();

        return $count === 0;
    }

    public function message(): string
    {
        return __('validation.unique');
    }
}
