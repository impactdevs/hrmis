<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            $table->uuid('company_job_id')->nullable();
            $table->foreign('company_job_id')
                  ->references('company_job_id')
                  ->on('company_jobs')
                  ->onDelete('set null');

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
            $table->string('home_district')->nullable();
            $table->string('sub_county')->nullable();
            $table->string('village')->nullable();
            $table->string('residency_type')->nullable();

            // Section 3: Work Background
            $table->string('present_department')->nullable();
            $table->string('present_post')->nullable();
            $table->date('date_of_appointment_present_post')->nullable();
            $table->string('terms_of_employment')->nullable();

            // Section 4: Family Background
            $table->string('marital_status')->nullable();

            // Section 5: Employment Record
            $table->json('employment_record')->nullable();

            // Section 6: Education & Training
            $table->json('education_training')->nullable();

            // Section 7: Criminal History
            $table->boolean('criminal_convicted')->default(false);
            $table->text('criminal_details')->nullable();

            // Section 8: Availability & Salary
            $table->string('availability')->nullable();
            $table->decimal('salary_expectation', 12, 2)->nullable();

            // Section 9: References
            $table->json('references')->nullable();
            $table->string('recommender_name')->nullable();
            $table->string('recommender_title')->nullable();

            // Section 10: Documents
            $table->json('academic_documents')->nullable();
            $table->string('cv')->nullable();
            $table->json('other_documents')->nullable();

            // Pipeline
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();

            // Scoring — populated by ApplicationScoringService on submit
            $table->unsignedTinyInteger('score')->nullable();       // 0-100
            $table->json('score_breakdown')->nullable();            // per-factor detail
            $table->boolean('meets_criteria')->nullable();          // passed all hard filters?
            $table->json('criteria_failures')->nullable();          // array of failure reasons
            $table->timestamp('scored_at')->nullable();

            // One submission per email per job
            $table->unique(['email', 'company_job_id'], 'unique_applicant_per_job');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};