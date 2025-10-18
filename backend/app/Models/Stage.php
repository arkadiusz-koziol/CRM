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
 *     schema="Stage",
 *     type="object",
 *     title="Stage",
 *     required={"id", "pipeline_id", "name", "order"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="pipeline_id", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="name", type="string", example="Prospecting"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Initial contact and qualification"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_final", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z")
 * )
 */
class Stage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'pipeline_id',
        'name',
        'description',
        'order',
        'is_final',
    ];

    protected function casts(): array
    {
        return [
            'is_final' => 'boolean',
        ];
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }
}
