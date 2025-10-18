<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class TrainingCategory extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'trainings_categories';

    protected $fillable = [
        'name',
    ];

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class, 'category_id');
    }
}
