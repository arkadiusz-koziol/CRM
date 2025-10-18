<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Contact",
 *     type="object",
 *     title="Contact",
 *     required={"id", "first_name", "last_name", "email", "lead_level", "source", "status"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Smith"),
 *     @OA\Property(property="email", type="string", format="email", example="john.smith@techcorp.com"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+1-555-0101"),
 *     @OA\Property(property="lead_level", type="string", enum={"lead", "contact"}, example="contact"),
 *     @OA\Property(property="owner_user_id", type="string", format="uuid", nullable=true, example="01234567-89ab-cdef-0123-456789abcdef"),
 *     @OA\Property(property="source", type="string", example="website"),
 *     @OA\Property(property="status", type="string", enum={"new", "active", "dormant", "lost"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z")
 * )
 */
class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'lead_level',
        'owner_user_id',
        'source',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'lead_level' => LeadLevel::class,
            'status' => ContactStatus::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'contact_company')
            ->withPivot(['position', 'is_primary'])
            ->withTimestamps();
    }
}
