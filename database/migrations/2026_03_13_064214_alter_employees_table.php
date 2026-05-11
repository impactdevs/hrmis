<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ---------------------------------------------------------------
        // STEP 1: Fix date_of_birth — convert string values to real dates.
        // The column may contain either 'Y-m-d' or 'Y-m-d H:i:s' formats
        // so we strip the time portion first with DATE() before converting.
        // Any unparseable values are set to null rather than crashing.
        // ---------------------------------------------------------------
        DB::statement("
            UPDATE employees
            SET date_of_birth = CASE
                WHEN date_of_birth IS NOT NULL AND date_of_birth != ''
                THEN DATE(date_of_birth)
                ELSE NULL
            END
        ");

        Schema::table('employees', function (Blueprint $table) {
            // Change date_of_birth from string to proper date column
            $table->date('date_of_birth')->nullable()->change();
        });

        // ---------------------------------------------------------------
        // STEP 2: Make first_name and last_name non-nullable.
        // Rows with null names get a placeholder so the constraint doesn't
        // fail — review and correct these records manually afterwards.
        // ---------------------------------------------------------------
        DB::table('employees')
            ->whereNull('first_name')
            ->update(['first_name' => 'UNKNOWN']);

        DB::table('employees')
            ->whereNull('last_name')
            ->update(['last_name' => 'UNKNOWN']);

        Schema::table('employees', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });

        // ---------------------------------------------------------------
        // STEP 3: Make staff_id non-nullable.
        // Rows missing a staff_id cannot be safely auto-filled — they are
        // flagged with a placeholder. Review these records manually.
        // ---------------------------------------------------------------
        DB::table('employees')
            ->whereNull('staff_id')
            ->update(['staff_id' => DB::raw("CONCAT('MISSING-', employee_id)")]);

        Schema::table('employees', function (Blueprint $table) {
            $table->string('staff_id')->nullable(false)->change();
        });

        // ---------------------------------------------------------------
        // STEP 4: Add proper foreign key constraints.
        // The inline syntax used originally created NO constraints at all.
        // ---------------------------------------------------------------
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('position_id')
                ->references('position_id')
                ->on('positions')
                ->nullOnDelete();

            $table->foreign('department_id')
                ->references('department_id')
                ->on('departments')
                ->nullOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['user_id']);

            $table->string('date_of_birth')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('staff_id')->nullable()->change();
        });
    }
};