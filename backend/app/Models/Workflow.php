<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Workflow extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'workflows';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(WorkflowRule::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(WorkflowAction::class);
    }
}
