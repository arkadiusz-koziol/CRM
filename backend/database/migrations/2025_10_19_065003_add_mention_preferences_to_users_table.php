<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('mention_notifications_enabled')->default(true);
            $table->boolean('mention_email_notifications_enabled')->default(true);
            $table->string('username')->unique()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mention_notifications_enabled',
                'mention_email_notifications_enabled',
                'username',
            ]);
        });
    }
};
