<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Estate",
 *     type="estate",
 *     title="Estate",
 *     required={"id", "name", "custom_id", "street", "postal_code", "city_id", "house_number"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Biedronka"),
 *     @OA\Property(property="custom_id", type="string", example="AZL123"),
 *     @OA\Property(property="street", type="string", example="Wrocławska"),
 *     @OA\Property(property="postal_code", type="string", example="00-000"),
 *     @OA\Property(property="city_id", type="integer", example="1"),
 *     @OA\Property(property="house_number", type="string", example="1a/2"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2024-10-11T08:30:00Z")
 * )
 */
class Estate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'custom_id',
        'street',
        'postal_code',
        'city_id',
        'house_number',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
