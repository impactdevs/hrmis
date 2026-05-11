<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ---------------------------------------------------------------
        // STEP 1: Drop created_at and updated_at if they exist.
        //
        // These are NOT clock-in/clock-out columns. They only record when
        // a database row was inserted/modified — not when an employee
        // actually badged the device.
        //
        // Clock-in = MIN(access_date_and_time) per staff_id + access_date
        // Clock-out = MAX(access_date_and_time) per staff_id + access_date
        //
        // access_date_and_time is the correct and only column needed for this.
        // ---------------------------------------------------------------
        if (Schema::hasColumn('attendances', 'created_at')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn(['created_at', 'updated_at']);
            });
        }

        // ---------------------------------------------------------------
        // STEP 2: Make staff_id non-nullable.
        // Any attendance rows with a null staff_id are orphaned records —
        // delete them as they cannot be linked to any employee.
        // ---------------------------------------------------------------
        $deleted = DB::table('attendances')->whereNull('staff_id')->count();
        if ($deleted > 0) {
            \Illuminate\Support\Facades\Log::warning(
                "alter_attendances_table: deleting {$deleted} attendance record(s) with null staff_id"
            );
            DB::table('attendances')->whereNull('staff_id')->delete();
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('staff_id')->nullable(false)->change();
        });

        // ---------------------------------------------------------------
        // STEP 3: Remove orphaned attendance rows whose staff_id does not
        // exist in the employees table, otherwise the FK constraint below
        // will fail.
        // ---------------------------------------------------------------
        $orphanCount = DB::table('attendances')
            ->whereNotIn('staff_id', DB::table('employees')->pluck('staff_id'))
            ->count();

        if ($orphanCount > 0) {
            \Illuminate\Support\Facades\Log::warning(
                "alter_attendances_table: deleting {$orphanCount} attendance record(s) with no matching employee"
            );
            DB::table('attendances')
                ->whereNotIn('staff_id', DB::table('employees')->pluck('staff_id'))
                ->delete();
        }

        // ---------------------------------------------------------------
        // STEP 4: Add FK constraint and performance indexes.
        // ---------------------------------------------------------------
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('staff_id')
                ->references('staff_id')
                ->on('employees')
                ->cascadeOnDelete();

            // Speeds up filtering by date (used on every index page load)
            $table->index('access_date');

            // Speeds up the clock-in/out summary query which groups
            // by staff_id + access_date
            $table->index(['staff_id', 'access_date']);
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropIndex(['access_date']);
            $table->dropIndex(['staff_id', 'access_date']);
            $table->string('staff_id')->nullable()->change();
            $table->timestamps();
        });
    }
};