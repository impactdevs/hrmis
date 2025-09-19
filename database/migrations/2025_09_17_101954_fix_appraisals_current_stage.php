<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Change current_stage to varchar
        Schema::table('appraisals', function (Blueprint $table) {
            $table->string('current_stage', 255)->default('Staff')->change();
        });

        // Step 2: Update 'Draft' to 'Staff'
        DB::table('appraisals')
            ->where('current_stage', 'Draft')
            ->update(['current_stage' => 'Staff']);

        // Step 3: Change back to enum
        Schema::table('appraisals', function (Blueprint $table) {
            $table->enum('current_stage', ['Staff', 'Head of Division', 'HR', 'Executive Secretary', 'Completed'])
                  ->default('Staff')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->enum('current_stage', ['Draft', 'Head of Division', 'HR', 'Executive Secretary', 'Completed'])
                  ->default('Draft')
                  ->change();
        });

        DB::table('appraisals')
            ->where('current_stage', 'Staff')
            ->update(['current_stage' => 'Draft']);
    }
};