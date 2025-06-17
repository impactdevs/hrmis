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
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('amount_applied_for')->nullable();
            $table->text('reasons')->nullable();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->date('repayment_start_date')->nullable();
            $table->date('repayment_end_date')->nullable();
            $table->date('date_of_contract_expiry')->nullable();
            $table->bigInteger('net_monthly_pay')->nullable();
            $table->bigInteger('outstanding_loan')->nullable();
            $table->text('comments')->nullable();
            $table->json('loan_request_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
