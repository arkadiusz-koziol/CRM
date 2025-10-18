<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Billing\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoices';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'nr',
        'contract_id',
        'company_id',
        'issue_date',
        'due_date',
        'amount',
        'currency',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'amount' => 'float',
            'status' => InvoiceStatus::class,
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
