<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the column if it exists
        if (Schema::hasColumn('appraisals', 'overall_assessment_and_comments')) {
            Schema::table('appraisals', function (Blueprint $table) {
                $table->dropColumn('overall_assessment_and_comments');
            });
        }

        // Add the column
        Schema::table('appraisals', function (Blueprint $table) {
            $table->text('overall_assessment_and_comments')->nullable()->after('panel_recommendations');
        });
    }

    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->dropColumn('overall_assessment_and_comments');
        });
    }
};
