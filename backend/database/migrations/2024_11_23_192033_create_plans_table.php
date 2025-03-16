<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estate_id');
            $table->string('file_path');
            $table->string('image_path');
            $table->timestamps();

            $table->foreign('estate_id')
                ->references('id')
                ->on('estates')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
