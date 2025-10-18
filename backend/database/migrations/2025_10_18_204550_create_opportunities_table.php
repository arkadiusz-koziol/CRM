<?php

declare(strict_types=1);

use App\Enums\Crm\OpportunityStatus;
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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->uuid('company_id');
            $table->uuid('contact_id')->nullable();
            $table->decimal('value', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('probability')->default(0); // 0-100
            $table->uuid('stage_id');
            $table->foreignId('owner_user_id');
            $table->date('close_date')->nullable();
            $table->enum('status', [
                OpportunityStatus::OPEN->value,
                OpportunityStatus::WON->value,
                OpportunityStatus::LOST->value,
            ])->default(OpportunityStatus::OPEN->value);
            $table->timestampsTz();
            $table->softDeletesTz();

            // Foreign key constraints
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('stage_id')->references('id')->on('stages')->onDelete('restrict');
            $table->foreign('owner_user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['company_id']);
            $table->index(['contact_id']);
            $table->index(['stage_id']);
            $table->index(['owner_user_id']);
            $table->index(['status']);
            $table->index(['close_date']);
            $table->index(['value']);
            $table->index(['probability']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
