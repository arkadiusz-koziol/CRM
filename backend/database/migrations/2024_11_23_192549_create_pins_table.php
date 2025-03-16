<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('x', 8, 2);
            $table->decimal('y', 8, 2);
            $table->string('photo_path')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('plan_id')->references('id')
                ->on('plans')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pins');
    }
};

