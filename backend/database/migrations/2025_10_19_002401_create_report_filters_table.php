<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_filters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('report_id');
            $table->string('field'); // field name to filter on
            $table->string('operator'); // eq, ne, gt, lt, gte, lte, in, not_in, like, between
            $table->json('value'); // filter value(s)
            $table->integer('order')->default(0); // filter order
            $table->timestampsTz();

            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->index(['report_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_filters');
    }
};
