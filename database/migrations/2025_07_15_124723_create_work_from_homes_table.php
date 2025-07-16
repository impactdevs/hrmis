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
        Schema::create('work_from_homes', function (Blueprint $table) {
            $table->uuid('work_from_home_id')->primary();
            $table->uuid('employee_id')->references('employee_id')->on('employees');
            $table->date('work_from_home_start_date')->nullable();
            $table->date('work_from_home_end_date')->nullable();
            $table->string('work_location')->nullable();
            $table->string('tasks_planned')->nullable();
            $table->string('work_from_home_reason')->nullable();
            $table->string('work_from_home_attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_from_homes');
    }
};
