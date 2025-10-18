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
        Schema::create('training_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->onDelete('cascade');
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['training_id']);
            $table->index(['file_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_files');
    }
};
