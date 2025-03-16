<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Pin",
 *     type="object",
 *     required={"plan_id", "x", "y", "user_id"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier of the pin",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="plan_id",
 *         type="integer",
 *         description="ID of the associated plan",
 *         example=101
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user who created the pin",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="x",
 *         type="number",
 *         format="float",
 *         description="X-coordinate of the pin on the plan",
 *         example=12.34
 *     ),
 *     @OA\Property(
 *         property="y",
 *         type="number",
 *         format="float",
 *         description="Y-coordinate of the pin on the plan",
 *         example=56.78
 *     ),
 *     @OA\Property(
 *         property="photo_path",
 *         type="string",
 *         nullable=true,
 *         description="Path to the photo associated with the pin",
 *         example="pins/photos/photo1.jpg"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the pin was created",
 *         example="2025-01-19T12:34:56Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the pin was last updated",
 *         example="2025-01-19T12:45:56Z"
 *     )
 * )
 */
class Pin extends Model
{
    protected $fillable = ['plan_id', 'x', 'y', 'photo_path', 'user_id'];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
