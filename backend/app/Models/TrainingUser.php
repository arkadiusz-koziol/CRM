<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingUser extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'training_user';

    protected $fillable = [
        'training_id',
        'user_id',
    ];

    protected $casts = [
        'training_id' => 'string',
        'user_id' => 'string',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
