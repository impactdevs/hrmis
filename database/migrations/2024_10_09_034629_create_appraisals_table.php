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
        Schema::create('appraisals', function (Blueprint $table) {
            $table->uuid('appraisal_id')->primary();
            $table->unsignedBigInteger('entry_id');
            $table->foreign('entry_id')->references(columns: 'id')->on('entries');
            $table->uuid('employee_id')->references('employee_id')->on('employees');
            $table->string('approval_status')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisals');
    }
};
