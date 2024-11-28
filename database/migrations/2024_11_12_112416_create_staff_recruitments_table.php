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
        Schema::create('staff_recruitments', function (Blueprint $table) {
            $table->uuid('staff_recruitment_id')->primary();
            $table->string('position');
            $table->uuid('department_id')->references('department_id')->on('departments');
            $table->integer('number_of_staff');
            $table->date('date_of_recruitment');
            $table->string('sourcing_method');
            $table->string('employment_basis');
            $table->string('funding_budget');
            $table->longText('justification');
            $table->json('approval_status')->nullable();
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
        Schema::dropIfExists('staff_recruitments');
    }
};
