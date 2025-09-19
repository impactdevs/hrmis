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
            $table->enum('current_stage', ['Staff', 'Head of Division', 'HR', 'Executive Secretary', 'Completed'])
                ->default('Staff') // Changed default to 'Draft'
                ->after('appraiser_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->dropColumn('current_stage');
        });
    }
};