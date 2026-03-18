<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedInteger('hikvision_id')
                ->nullable()
                ->unique()
                ->after('staff_id')
                ->comment('Numeric-only ID used by HIKVision DS-K1T342 device (1-99999999)');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('hikvision_id');
        });
    }
};
