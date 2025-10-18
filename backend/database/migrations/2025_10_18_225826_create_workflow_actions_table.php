<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workflow_id');
            $table->string('name');
            $table->string('type');
            $table->json('config');
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('workflow_id')->references('id')->on('workflows')->onDelete('cascade');
            $table->index(['workflow_id', 'is_active', 'deleted_at']);
            $table->index(['type', 'is_active', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_actions');
    }
};
