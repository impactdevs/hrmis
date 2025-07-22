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
        Schema::create('off_desks', function (Blueprint $table) {
            $table->uuid('off_desk_id')->primary();
            $table->uuid('employee_id')->references('employee_id')->on('employees');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->text('reason')->nullable();
            $table->text('destination')->nullable();
            $table->text('duty_allocated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('off_desks');
    }
};
