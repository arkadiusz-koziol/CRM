<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('custom_id');
            $table->string('street');
            $table->string('postal_code');
            $table->unsignedBigInteger('city_id');
            $table->string('house_number');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estates');
    }
};
