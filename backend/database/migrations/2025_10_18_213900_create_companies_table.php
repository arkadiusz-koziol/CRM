<?php

declare(strict_types=1);

use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
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
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->enum('source', [
                CompanySource::WEBSITE->value,
                CompanySource::REFERRAL->value,
                CompanySource::SOCIAL_MEDIA->value,
                CompanySource::EMAIL_CAMPAIGN->value,
                CompanySource::COLD_CALL->value,
                CompanySource::TRADE_SHOW->value,
                CompanySource::PARTNER->value,
                CompanySource::OTHER->value,
            ]);
            $table->enum('status', [
                CompanyStatus::ACTIVE->value,
                CompanyStatus::INACTIVE->value,
                CompanyStatus::PROSPECT->value,
            ])->default(CompanyStatus::PROSPECT->value);
            $table->string('region')->nullable();
            $table->string('vat_id')->nullable()->unique();
            $table->foreignId('created_by');
            $table->timestampsTz();
            $table->softDeletesTz();

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['vat_id']);
            $table->index(['name']);
            $table->index(['status', 'source']);
            $table->index(['region']);
            $table->index(['created_by']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
