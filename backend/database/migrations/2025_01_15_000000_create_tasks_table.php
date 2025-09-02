<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('status', [
                TaskStatus::PENDING->value,
                TaskStatus::IN_PROGRESS->value,
                TaskStatus::COMPLETED->value,
                TaskStatus::CANCELLED->value,
                TaskStatus::ON_HOLD->value
            ])->default(TaskStatus::PENDING->value);
            $table->enum('priority', [
                TaskPriority::LOW->value,
                TaskPriority::MEDIUM->value,
                TaskPriority::HIGH->value,
                TaskPriority::URGENT->value
            ])->default(TaskPriority::MEDIUM->value);
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
