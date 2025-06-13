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
        Schema::table('company_jobs', function (Blueprint $table) {
            $table->datetime('will_become_active_at')->nullable()->after('job_title');
            $table->datetime('will_become_inactive_at')->nullable()->after('will_become_active_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_jobs', function (Blueprint $table) {
            //
        });
    }
};
