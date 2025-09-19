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
        Schema::create('appraisal_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('appraisal_id');
            $table->foreign('appraisal_id')->references('appraisal_id')->on('appraisals')->onDelete('cascade');
            $table->uuid('actor_id'); // Employee who performed the action
            $table->foreign('actor_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->string('action'); // created, submitted, approved, rejected, withdrawn, edited
            $table->string('stage_from')->nullable(); // Previous stage
            $table->string('stage_to')->nullable(); // New stage
            $table->string('actor_role'); // Role of the person performing action
            $table->text('comments')->nullable(); // Additional comments or rejection reason
            $table->json('metadata')->nullable(); // Additional data like changed fields
            $table->timestamps();

            // Indexes for better performance
            $table->index(['appraisal_id', 'created_at']);
            $table->index(['actor_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisal_history');
    }
};
