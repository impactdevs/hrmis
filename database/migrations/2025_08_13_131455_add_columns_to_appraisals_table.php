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
            // add panel_recommendations column and supervisor_recommendations column
            $table->text('panel_recommendations')->nullable()->after('superviser_overall_assessment');
            $table->text('supervisor_recommendations')->nullable()->after('panel_recommendationst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            //
        });
    }
};
