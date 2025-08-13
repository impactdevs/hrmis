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
        Schema::table('appraisals', function (Blueprint $table) {
            // Drop columns if they already exist
            if (Schema::hasColumn('appraisals', 'panel_recommendations')) {
                $table->dropColumn('panel_recommendations');
            }
            if (Schema::hasColumn('appraisals', 'supervisor_recommendations')) {
                $table->dropColumn('supervisor_recommendations');
            }
        });

        Schema::table('appraisals', function (Blueprint $table) {
            // Add the new columns
            $table->text('panel_recommendations')->nullable()->after('recommendations');
            $table->text('supervisor_recommendations')->nullable()->after('recommendations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->dropColumn(['panel_recommendations', 'supervisor_recommendations']);
        });
    }
};
