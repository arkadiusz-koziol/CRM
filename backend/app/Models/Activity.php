<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

final class Activity extends Model
{
    use HasUuids;

    protected $table = 'activities';

    protected $fillable = [
        'id',
        'action',
        'user_name',
        'user_email',
        'entity_type',
        'entity_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;
}
