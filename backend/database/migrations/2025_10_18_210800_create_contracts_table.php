<?php

declare(strict_types=1);

use App\Enums\Billing\ContractStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nr')->unique();
            $table->uuid('company_id');
            $table->date('start_at');
            $table->date('end_at');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', [
                ContractStatus::DRAFT->value,
                ContractStatus::ACTIVE->value,
                ContractStatus::EXPIRED->value,
                ContractStatus::TERMINATED->value,
            ])->default(ContractStatus::DRAFT->value);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->index(['nr']);
            $table->index(['company_id']);
            $table->index(['start_at']);
            $table->index(['end_at']);
            $table->index(['status']);
            $table->index(['deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
