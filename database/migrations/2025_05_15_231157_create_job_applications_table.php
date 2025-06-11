<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            // Section 1: Post & Personal Details
            $table->string('post_applied')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('full_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();

            // Section 2: Nationality & Residence
            $table->string('nationality')->nullable();
            $table->string('nin')->nullable();
            $table->string('residency_type')->nullable();

            // Section 4: Family Background
            $table->string('marital_status')->nullable();

            // Section 5: Education training
            $table->json('education_training')->nullable();


            // Section 7 (duplicate number in HTML): Employment Record
            $table->json('employment_record')->nullable();

            // Section 8: Criminal History
            $table->boolean('criminal_convicted')->default(false);
            $table->text('criminal_details')->nullable();

            // Section 9: Availability & Salary
            $table->string('availability')->nullable();
            $table->decimal('salary_expectation', 10, 2)->nullable();

            // Section 10: References
            $table->json('references')->nullable();
            $table->string('recommender_name')->nullable();
            $table->string('recommender_title')->nullable();

            $table->json('academic_documents');
            $table->string('cv');
            $table->json('other_documents')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_applications');
    }
};
