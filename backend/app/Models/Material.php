<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *     schema="Material",
 *     type="object",
 *     title="Material",
 *     required={"id", "name", "description", "count", "price","created_at", "updated_at", "deleted_at"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Nail"),
 *     @OA\Property(property="description", type="string", example="Big head, made from steel, wood-use."),
 *     @OA\Property(property="count", type="int8", example="1000000"),
 *     @OA\Property(property="price", type="int8", example="10000"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2024-10-10T08:30:00Z"),
 * )
 */

class Material extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'count',
        'price',
    ];
}
