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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('appraisals', 'reminder_sent')) {
                $table->boolean('reminder_sent')->default(false)->after('current_stage');
            }
            
            if (!Schema::hasColumn('appraisals', 'last_reminder_sent')) {
                $table->timestamp('last_reminder_sent')->nullable()->after('reminder_sent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            if (Schema::hasColumn('appraisals', 'reminder_sent')) {
                $table->dropColumn('reminder_sent');
            }
            
            if (Schema::hasColumn('appraisals', 'last_reminder_sent')) {
                $table->dropColumn('last_reminder_sent');
            }
        });
    }
};
