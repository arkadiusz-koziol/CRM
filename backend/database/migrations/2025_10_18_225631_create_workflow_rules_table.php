<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workflow_id');
            $table->string('name');
            $table->text('description');
            $table->json('conditions');
            $table->json('actions');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('workflow_id')->references('id')->on('workflows')->onDelete('cascade');
            $table->index(['workflow_id', 'is_active', 'deleted_at']);
            $table->index(['priority', 'is_active', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_rules');
    }
};
