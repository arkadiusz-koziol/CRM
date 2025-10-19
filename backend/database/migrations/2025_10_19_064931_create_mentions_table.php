<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('comment_id');
            $table->foreignId('mentioned_user_id');
            $table->foreignId('mentioner_user_id');
            $table->string('entity_type');
            $table->uuid('entity_id');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('mentioned_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mentioner_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['mentioned_user_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['comment_id']);
            $table->unique(['comment_id', 'mentioned_user_id'], 'unique_mention_per_comment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentions');
    }
};
