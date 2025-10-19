<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('report_id');
            $table->unsignedBigInteger('run_by');
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->json('parameters')->nullable(); // runtime parameters for the report
            $table->string('file_path')->nullable(); // path to generated file
            $table->string('file_name')->nullable(); // original file name
            $table->integer('file_size')->nullable(); // file size in bytes
            $table->string('mime_type')->nullable(); // MIME type of the file
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestampsTz();

            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('run_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['report_id', 'status']);
            $table->index(['run_by', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_runs');
    }
};
