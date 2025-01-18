<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = ['estate_id', 'file_path', 'image_path'];

    public function estate(): BelongsTo
    {
        return $this->belongsTo(Estate::class);
    }

    public function pins(): HasMany
    {
        return $this->hasMany(Pin::class);
    }
}
