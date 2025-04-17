<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingCategory extends Model
{
    protected $fillable = ['name'];

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class, 'category_id');
    }
}
