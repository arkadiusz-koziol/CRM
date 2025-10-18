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
        Schema::create('contact_company', function (Blueprint $table) {
            $table->id();
            $table->uuid('contact_id');
            $table->uuid('company_id');
            $table->string('position')->nullable(); // Job title/position at the company
            $table->boolean('is_primary')->default(false); // Primary company for the contact
            $table->timestampsTz();
            $table->softDeletesTz();

            // Foreign key constraints
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Unique constraint to prevent duplicate relationships
            $table->unique(['contact_id', 'company_id', 'deleted_at'], 'contact_company_unique');

            // Indexes for better performance
            $table->index(['contact_id']);
            $table->index(['company_id']);
            $table->index(['is_primary']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_company');
    }
};
