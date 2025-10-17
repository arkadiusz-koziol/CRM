<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     title="Task",
 *     required={"id", "title", "description", "status", "priority", "assigned_to", "created_by"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Fix login issue"),
 *     @OA\Property(property="description", type="string", example="Users cannot log in to the system"),
 *     @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed",
 *      "cancelled", "on_hold"}, example="pending"),
 *     @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "urgent"}, example="high"),
 *     @OA\Property(property="assigned_to", type="integer", example=2),
 *     @OA\Property(property="created_by", type="integer", example=1),
 *     @OA\Property(property="due_date", type="string", format="date-time", example="2024-12-31T23:59:59Z"),
 *     @OA\Property(property="completed_at", type="string", format="date-time", example="2024-12-30T15:30:00Z"),
 *     @OA\Property(property="estimated_hours", type="number", format="float", example=4.5),
 *     @OA\Property(property="actual_hours", type="number", format="float", example=5.0),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-06T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-06T08:30:00Z")
 * )
 */
class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'created_by',
        'due_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
            'estimated_hours' => 'decimal:2',
            'actual_hours' => 'decimal:2',
        ];
    }

    /**
     * Get the user assigned to this task.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this task.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the task ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the task title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the task description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the task status.
     */
    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    /**
     * Get the task priority.
     */
    public function getPriority(): TaskPriority
    {
        return $this->priority;
    }

    /**
     * Get the assigned user ID.
     */
    public function getAssignedTo(): int
    {
        return $this->assigned_to;
    }

    /**
     * Get the creator user ID.
     */
    public function getCreatedBy(): int
    {
        return $this->created_by;
    }

    /**
     * Get the due date.
     */
    public function getDueDate(): ?string
    {
        return $this->due_date?->toDateTimeString();
    }

    /**
     * Get the completion date.
     */
    public function getCompletedAt(): ?string
    {
        return $this->completed_at?->toDateTimeString();
    }

    /**
     * Get the estimated hours.
     */
    public function getEstimatedHours(): ?float
    {
        return $this->estimated_hours;
    }

    /**
     * Get the actual hours.
     */
    public function getActualHours(): ?float
    {
        return $this->actual_hours;
    }

    /**
     * Get the creation date.
     */
    public function getCreatedAt(): ?string
    {
        return $this->created_at?->toDateTimeString();
    }

    /**
     * Get the update date.
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at?->toDateTimeString();
    }
}
