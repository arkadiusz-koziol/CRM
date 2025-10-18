<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Pipeline",
 *     type="object",
 *     title="Pipeline",
 *     required={"id", "name", "is_default", "created_by"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="name", type="string", example="Sales Pipeline"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Main sales pipeline for our CRM"),
 *     @OA\Property(property="is_default", type="boolean", example=true),
 *     @OA\Property(property="created_by", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z")
 * )
 */
class Pipeline extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'is_default',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class)->orderBy('order');
    }

    public function opportunities(): HasMany
    {
        return $this->hasManyThrough(Opportunity::class, Stage::class);
    }
}
