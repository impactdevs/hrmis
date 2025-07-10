<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whistleblowing_reports', function (Blueprint $table) {
            $table->id();
            $table->string('submission_type', 255);
            $table->text('description');
            $table->text('individuals_involved');
            $table->text('evidence_details');
            $table->string('evidence_file_path')->nullable();
            $table->enum('reported_before', ['Yes', 'No', 'I do not know']);
            $table->text('reported_details')->nullable();
            $table->text('suggested_resolution')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->string('tracking_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whistleblowing_reports');
    }
};