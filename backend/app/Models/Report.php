<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Reports\ReportSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'source',
        'columns',
        'filters',
        'sorting',
        'created_by',
        'is_public',
    ];

    protected $casts = [
        'columns' => 'array',
        'filters' => 'array',
        'sorting' => 'array',
        'is_public' => 'boolean',
        'source' => ReportSource::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ReportRun::class);
    }

    public function filters(): HasMany
    {
        return $this->hasMany(ReportFilter::class);
    }

    public function latestRun(): HasMany
    {
        return $this->hasMany(ReportRun::class)->latest();
    }
}
