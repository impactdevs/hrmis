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
        Schema::create('out_station_trainings', function (Blueprint $table) {
            $table->uuid('training_id')->primary();
            $table->string('destination')->nullable();
            $table->string('travel_purpose')->nullable();
            $table->json('relevant_documents')->nullable();
            $table->date('departure_date');
            $table->date('return_date');
            $table->longText('sponsor')->nullable();
            $table->string('hotel')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->json('my_work_will_be_done_by');
            $table->json('training_request_status')->nullable();
            $table->longText('rejection_reason')->nullable();
            $table->uuid('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('out_station_trainings');
    }
};
