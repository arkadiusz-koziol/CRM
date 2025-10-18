<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Company",
 *     type="object",
 *     title="Company",
 *     required={"id", "name", "source", "status", "created_by"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="name", type="string", example="TechCorp Solutions"),
 *     @OA\Property(property="industry", type="string", nullable=true, example="Technology"),
 *     @OA\Property(property="source", type="string", enum={"website", "referral", "social_media", "email_campaign", "cold_call", "trade_show", "partner", "other"}, example="website"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "prospect"}, example="active"),
 *     @OA\Property(property="region", type="string", nullable=true, example="North America"),
 *     @OA\Property(property="vat_id", type="string", nullable=true, example="US123456789"),
 *     @OA\Property(property="created_by", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z")
 * )
 */
class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'industry',
        'source',
        'status',
        'region',
        'vat_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'source' => CompanySource::class,
            'status' => CompanyStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_company')
            ->withPivot(['position', 'is_primary'])
            ->withTimestamps();
    }
}
