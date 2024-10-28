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
        Schema::create('form_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('form_id');
            $table->foreign('form_id')->references('uuid')->on('forms')->onDelete('cascade');
            $table->unsignedBigInteger('title')->nullable();
            $table->foreign('title')->references('id')->on('form_fields')->onDelete('cascade');
            $table->unsignedBigInteger('subtitle')->nullable();
            $table->foreign('subtitle')->references('id')->on('form_fields')->onDelete('cascade');
            $table->boolean('is_published')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_settings');
    }
};
