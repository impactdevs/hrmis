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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('task_id')->primary();
            $table->uuid('work_from_home_id');
            $table->date('task_start_date')->nullable();
            $table->date('task_end_date')->nullable();
            $table->longtext('description')->nullable();
            $table->timestamps();

             $table->foreign('work_from_home_id')->references('work_from_home_id')->on('work_from_homes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
