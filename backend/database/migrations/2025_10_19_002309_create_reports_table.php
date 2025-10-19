<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('source'); // users, companies, contacts, tasks, opportunities, invoices
            $table->json('columns'); // whitelist of allowed columns
            $table->json('filters')->nullable(); // JSON filter conditions
            $table->json('sorting')->nullable(); // JSON sorting configuration
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_public')->default(false);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['source', 'created_by']);
            $table->index(['is_public', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
