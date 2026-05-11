<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_jobs', function (Blueprint $table) {
            $table->uuid('company_job_id')->primary();
            $table->string('job_code')->unique();
            $table->string('job_title');
            $table->longText('job_description')->nullable();

            // Shareable public token — the link HR shares is /apply/{public_token}
            $table->string('public_token', 64)->unique();

            // Active window
            $table->datetime('will_become_active_at')->nullable();
            $table->datetime('will_become_inactive_at')->nullable();

            // ── Screening Criteria ───────────────────────────────────────────
            // Hard filters — failing any disqualifies the application immediately
            $table->string('criteria_min_qualification')->nullable(); // Certificate|Diploma|Degree|Masters|PhD
            $table->integer('criteria_min_experience_years')->nullable();
            $table->integer('criteria_min_age')->nullable();
            $table->integer('criteria_max_age')->nullable();
            // Keywords matched against education_training + employment_record text
            $table->json('criteria_required_keywords')->nullable();

            // Scoring weights — how much each factor contributes to the 0-100 score
            // These are stored as integers and normalised at scoring time
            $table->unsignedTinyInteger('weight_qualification')->default(30);
            $table->unsignedTinyInteger('weight_experience')->default(40);
            $table->unsignedTinyInteger('weight_keyword_match')->default(20);
            $table->unsignedTinyInteger('weight_age_fit')->default(10);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_jobs');
    }
};