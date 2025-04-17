<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingFile extends Model
{
    protected $fillable = ['training_id', 'file_path'];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}
