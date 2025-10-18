<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pipeline_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_final')->default(false);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('pipeline_id')->references('id')->on('pipelines')->onDelete('cascade');

            $table->index(['pipeline_id', 'order']);
            $table->index(['pipeline_id', 'is_final']);
            $table->index(['deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
