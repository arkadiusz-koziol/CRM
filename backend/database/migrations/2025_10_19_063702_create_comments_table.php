<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('content');
            $table->text('content_html')->nullable(); // Sanitized HTML version
            $table->string('commentable_type'); // Polymorphic relationship
            $table->uuid('commentable_id'); // Polymorphic relationship
            $table->unsignedBigInteger('author_id'); // User who wrote the comment
            $table->uuid('parent_id')->nullable(); // For nested comments/replies
            $table->boolean('is_private')->default(false); // ACL visibility
            $table->json('mentions')->nullable(); // @user mentions
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['commentable_type', 'commentable_id']);
            $table->index(['author_id', 'created_at']);
            $table->index(['parent_id']);
            $table->index(['is_private', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
