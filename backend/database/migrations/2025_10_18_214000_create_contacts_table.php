<?php

declare(strict_types=1);

use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('lead_level', [
                LeadLevel::LEAD->value,
                LeadLevel::CONTACT->value,
            ])->default(LeadLevel::LEAD->value);
            $table->foreignId('owner_user_id')->nullable();
            $table->string('source');
            $table->enum('status', [
                ContactStatus::NEW->value,
                ContactStatus::ACTIVE->value,
                ContactStatus::DORMANT->value,
                ContactStatus::LOST->value,
            ])->default(ContactStatus::NEW->value);
            $table->timestampsTz();
            $table->softDeletesTz();

            // Foreign key constraints
            $table->foreign('owner_user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['email']);
            $table->index(['first_name', 'last_name']);
            $table->index(['status', 'lead_level']);
            $table->index(['owner_user_id']);
            $table->index(['source']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
