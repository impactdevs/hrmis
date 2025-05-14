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
        Schema::create('appraisals', function (Blueprint $table) {
            $table->uuid('appraisal_id')->primary();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');

            $table->uuid('appraiser_id');
            $table->foreign('appraiser_id')->references('employee_id')->on('employees')->onDelete('cascade');


            // Type of Review
            $table->string('review_type')->nullable();
            $table->date('appraisal_start_date')->nullable();
            $table->date('appraisal_end_date')->nullable();

            // Section 1
            $table->string('job_compatibility')->nullable();
            $table->text('if_no_job_compatibility')->nullable();
            $table->text('unanticipated_constraints')->nullable();
            $table->text('personal_initiatives')->nullable();
            $table->text('training_support_needs')->nullable();
            $table->json('appraisal_period_rate')->nullable();

            // Section 2
            $table->json('personal_attributes_assessment')->nullable();

            // Performance Planning
            $table->json('performance_planning')->nullable();

            // Section 3
            $table->text('employee_strength')->nullable();
            $table->text('employee_improvement')->nullable();
            $table->text('superviser_overall_assessment')->nullable();
            $table->text('recommendations')->nullable();

            // Review Panel
            $table->text('panel_comment')->nullable();
            $table->text('panel_recommendation')->nullable();
            $table->text('overall_assessment')->nullable();

            // Executive Secretary
            $table->text('executive_secretary_comments')->nullable();

            $table->json('appraisal_request_status')->nullable();

            $table->longText('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisals');
    }
};
