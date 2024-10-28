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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('event_id')->primary();
            $table->date('event_start_date')->nullable();
            $table->date('event_end_date')->nullable();
            $table->string('event_title')->nullable();
            $table->longText('event_description')->nullable();
            $table->string('event_location')->nullable();
            $table->json('category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
