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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid('application_id')->primary();
            $table->uuid('company_job_id')->references('company_job_id')->on('company_jobs');
            $table->unsignedBigInteger('entry_id');
            $table->foreign('entry_id')->references(columns: 'id')->on('entries');
            $table->string('application_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
