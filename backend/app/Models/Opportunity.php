<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Crm\OpportunityStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Opportunity",
 *     type="object",
 *     title="Opportunity",
 *     required={"id", "title", "company_id", "value", "currency", "probability", "stage_id", "owner_user_id", "status"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="title", type="string", example="Enterprise Software License"),
 *     @OA\Property(property="company_id", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="contact_id", type="string", format="uuid", nullable=true, example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="value", type="number", format="float", example=50000.00),
 *     @OA\Property(property="currency", type="string", example="USD"),
 *     @OA\Property(property="probability", type="integer", example=75),
 *     @OA\Property(property="stage_id", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="owner_user_id", type="integer", example=1),
 *     @OA\Property(property="close_date", type="string", format="date", nullable=true, example="2024-12-31"),
 *     @OA\Property(property="status", type="string", enum={"open", "won", "lost"}, example="open"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z")
 * )
 */
class Opportunity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'company_id',
        'contact_id',
        'value',
        'currency',
        'probability',
        'stage_id',
        'owner_user_id',
        'close_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'probability' => 'integer',
            'close_date' => 'date',
            'status' => OpportunityStatus::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
