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
        Schema::create('leaves', function (Blueprint $table) {
            $table->uuid('leave_id')->primary();
            $table->uuid('user_id')->references('id')->on('users');
            $table->date('start_date');
            $table->date('end_date');
            $table->uuid('leave_type_id')->references('leave_type_id')->on('leave_types');
            $table->string('reason');
            $table->string('leave_request_status')->default('HR');
            $table->json('my_work_will_be_done_by');
            $table->longText('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
