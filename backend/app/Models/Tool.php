<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Tool",
 *     type="object",
 *     title="Tool",
 *     required={"id", "name", "description", "count", "created_at", "updated_at", "deleted_at"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Hammer"),
 *     @OA\Property(property="description", type="string", example="Black, small, wood-made"),
 *     @OA\Property(property="count", type="int8", example="100"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2024-10-10T08:30:00Z"),
 * )
 */

class Tool extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'count',
    ];
}
