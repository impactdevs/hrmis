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
        Schema::create('field_properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_id');
            $table->foreign('field_id')->references('id')->on('form_fields')->onDelete('cascade');
            $table->unsignedBigInteger('conditional_visibility_field_id');
            $table->foreign('conditional_visibility_field_id')->references('id')->on('form_fields')->onDelete('cascade');
            $table->string('conditional_visibility_operator');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_properties');
    }
};
