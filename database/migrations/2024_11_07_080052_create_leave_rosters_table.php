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
        Schema::create('leave_rosters', function (Blueprint $table) {
            $table->uuid('leave_roster_id')->primary();
            $table->uuid('employee_id')->references('employee_id')->on('employees');
            $table->json('months')->nullable();
            $table->integer('year')->default(date('Y'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_rosters');
    }
};
