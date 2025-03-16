<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Plan",
 *     type="object",
 *     required={"estate_id", "file_path", "image_path"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier of the plan",
 *         example=101
 *     ),
 *     @OA\Property(
 *         property="estate_id",
 *         type="integer",
 *         description="ID of the associated estate",
 *         example=12
 *     ),
 *     @OA\Property(
 *         property="file_path",
 *         type="string",
 *         description="Path to the PDF file of the plan",
 *         example="plans/pdf/plan1.pdf"
 *     ),
 *     @OA\Property(
 *         property="image_path",
 *         type="string",
 *         description="Path to the image version of the plan",
 *         example="plans/images/plan1.jpg"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the plan was created",
 *         example="2025-01-19T12:34:56Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the plan was last updated",
 *         example="2025-01-19T12:45:56Z"
 *     )
 * )
 */
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
