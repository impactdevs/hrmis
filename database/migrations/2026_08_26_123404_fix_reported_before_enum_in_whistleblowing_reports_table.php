<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The enum was created as ['Yes', 'No', 'I do not know'], but the form and
 * its validation rule have always sent "I don't know" — a value the enum
 * never accepted, so every submission of that option failed with a SQL
 * data-truncation error. Align the enum to the value actually in use.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE whistleblowing_reports MODIFY reported_before ENUM('Yes', 'No', 'I don''t know') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE whistleblowing_reports MODIFY reported_before ENUM('Yes', 'No', 'I do not know') NOT NULL");
    }
};
