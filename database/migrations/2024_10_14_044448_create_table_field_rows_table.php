<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('table_field_rows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_id')->references('id')->on('form_fields')->onDelete('cascade');
            $table->json('table_row');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_field_rows');
    }
};
