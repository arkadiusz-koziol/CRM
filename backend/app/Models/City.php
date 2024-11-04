<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="City",
 *     type="object",
 *     title="City",
 *     required={"id", "name", "district", "commune", "voivodeship"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Wrocław"),
 *     @OA\Property(property="district", type="string", example="Wrocław"),
 *     @OA\Property(property="commune", type="string", example="Wrocław"),
 *     @OA\Property(property="voivodeship", type="string", example="Dolnośląskie"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2024-10-11T08:30:00Z")
 * )
 */
class City extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'district',
        'commune',
        'voivodeship',
    ];
}
