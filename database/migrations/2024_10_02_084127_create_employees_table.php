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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('employee_id')->primary();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('title')->nullable();
            $table->string('staff_id')->nullable()->unique();
            $table->uuid('position_id')->nullable()->references('position_id')->on('positions');
            $table->string('nin')->nullable()->unique();
            $table->date('date_of_entry')->nullable();
            $table->date('contract_expiry_date')->nullable();
            $table->uuid('department_id')->nullable()->references('department_id')->on('departments');
            $table->string('nssf_no')->nullable();
            $table->string('home_district')->nullable();
            $table->json('qualifications_details')->nullable();
            $table->string('tin_no')->nullable();
            $table->longText('job_description')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number')->nullable()->unique();
            $table->string('next_of_kin')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
