<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Collab\Entity\Mention as MentionEntity;
use App\Infrastructure\Collab\MentionMapper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Mention extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'comment_id',
        'mentioned_user_id',
        'mentioner_user_id',
        'entity_type',
        'entity_id',
        'notified_at',
        'read_at',
    ];

    protected $casts = [
        'id' => 'string',
        'comment_id' => 'string',
        'mentioned_user_id' => 'string',
        'mentioner_user_id' => 'string',
        'entity_id' => 'string',
        'notified_at' => 'datetime',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function mentionedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }

    public function mentionerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentioner_user_id');
    }

    public function toDomain(): MentionEntity
    {
        return MentionMapper::toDomain($this);
    }

    public static function fromDomain(MentionEntity $mention): self
    {
        return MentionMapper::toModel($mention);
    }
}
