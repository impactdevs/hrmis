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
        Schema::create('employee_qualifications', function (Blueprint $table) {
            $table->uuid('qualification_id')->primary();
            $table->uuid('employee_id');

            $table->foreign('employee_id')
                ->references('employee_id')   // <-- quotes around column
                ->on('employees')             // <-- quotes around table
                ->onDelete('cascade');

            $table->string('qualification');   // e.g. "CPA"
            $table->string('institution')->nullable();
            $table->year('year_obtained')->nullable();

            $table->timestamps(); // adds created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_qualifications');
    }
};
