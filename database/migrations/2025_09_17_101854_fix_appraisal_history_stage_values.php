<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Change stage_from and stage_to to varchar to avoid enum constraint
        Schema::table('appraisal_history', function (Blueprint $table) {
            $table->string('stage_from', 255)->nullable()->change();
            $table->string('stage_to', 255)->nullable()->change();
        });

        // Step 2: Update 'Draft' to 'Staff'
        DB::table('appraisal_history')
            ->where('stage_from', 'Draft')
            ->update(['stage_from' => 'Staff']);

        DB::table('appraisal_history')
            ->where('stage_to', 'Draft')
            ->update(['stage_to' => 'Staff']);

        // Step 3: Change back to enum with new values
        Schema::table('appraisal_history', function (Blueprint $table) {
            $table->enum('stage_from', ['Staff', 'Head of Division', 'HR', 'Executive Secretary', 'Completed'])
                  ->nullable()
                  ->change();
            $table->enum('stage_to', ['Staff', 'Head of Division', 'HR', 'Executive Secretary', 'Completed'])
                  ->nullable()
                  ->change();
        });
    }

    public function down(): void
    {
        // Revert to old enum
        Schema::table('appraisal_history', function (Blueprint $table) {
            $table->enum('stage_from', ['Draft', 'Head of Division', 'HR', 'Executive Secretary', 'Completed'])
                  ->nullable()
                  ->change();
            $table->enum('stage_to', ['Draft', 'Head of Division', 'HR', 'Executive Secretary', 'Completed'])
                  ->nullable()
                  ->change();
        });

        // Revert 'Staff' to 'Draft'
        DB::table('appraisal_history')
            ->where('stage_from', 'Staff')
            ->update(['stage_from' => 'Draft']);

        DB::table('appraisal_history')
            ->where('stage_to', 'Staff')
            ->update(['stage_to' => 'Draft']);
    }
};