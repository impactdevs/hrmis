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
        Schema::create('leave_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('leave_id')->references('leave_id')->on('leaves');
            $table->uuid('actor_id')->nullable(); // The employee who performed the action
            $table->string('action'); // submitted, approved, rejected, withdrawn, etc.
            $table->string('stage_from')->nullable(); // previous stage
            $table->string('stage_to')->nullable(); // new stage
            $table->string('actor_role')->nullable(); // role of the actor
            $table->text('comments')->nullable(); // rejection reason, approval notes, etc.
            $table->json('metadata')->nullable(); // additional data
            $table->timestamps();
            
            $table->index(['leave_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_history');
    }
};
