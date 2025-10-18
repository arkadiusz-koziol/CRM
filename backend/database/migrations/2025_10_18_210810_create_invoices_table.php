<?php

declare(strict_types=1);

use App\Enums\Billing\InvoiceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nr')->unique();
            $table->uuid('contract_id')->nullable();
            $table->uuid('company_id');
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', [
                InvoiceStatus::ISSUED->value,
                InvoiceStatus::PAID->value,
                InvoiceStatus::OVERDUE->value,
                InvoiceStatus::CANCELLED->value,
            ])->default(InvoiceStatus::ISSUED->value);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->index(['nr']);
            $table->index(['contract_id']);
            $table->index(['company_id']);
            $table->index(['issue_date']);
            $table->index(['due_date']);
            $table->index(['status']);
            $table->index(['deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
