<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Billing\ContractStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contracts';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'nr',
        'company_id',
        'start_at',
        'end_at',
        'amount',
        'currency',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'date',
            'end_at' => 'date',
            'amount' => 'float',
            'status' => ContractStatus::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
