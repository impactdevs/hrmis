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
        Schema::create('appraisal_drafts', function (Blueprint $table) {
            $table->id();
            $table->uuid('appraisal_id');
            $table->foreign('appraisal_id')->references('appraisal_id')->on('appraisals')->onDelete('cascade');
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisal_drafts');
    }
};
