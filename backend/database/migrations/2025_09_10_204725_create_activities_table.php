<?php

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
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('action');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('entity_type');
            $table->string('entity_id')->nullable();
            $table->timestamp('created_at');

            $table->index(['entity_type', 'created_at']);
            $table->index(['user_email', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
