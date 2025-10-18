<?php

declare(strict_types=1);

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
        Schema::create('pipelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by');
            $table->timestampsTz();
            $table->softDeletesTz();

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['name']);
            $table->index(['is_default']);
            $table->index(['created_by']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipelines');
    }
};
