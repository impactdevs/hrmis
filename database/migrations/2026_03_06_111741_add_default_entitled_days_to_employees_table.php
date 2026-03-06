<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Stores the employee's base entitlement, used to reset entitled_leave_days each year
            $table->integer('default_entitled_days')->nullable()->after('entitled_leave_days');
        });

        // Seed default_entitled_days from current entitled_leave_days for all existing employees
        DB::statement('UPDATE employees SET default_entitled_days = entitled_leave_days');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('default_entitled_days');
        });
    }
};
