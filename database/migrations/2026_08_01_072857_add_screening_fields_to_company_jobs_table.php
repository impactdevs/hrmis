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
            // Shareable link for external screening board members — separate from
            // public_token (the candidate-facing application link).
            $table->uuid('screening_token')->nullable()->unique()->after('public_token');
            // Hashed shared PIN the board enters once before viewing applicants.
            $table->string('screening_pin')->nullable()->after('screening_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_jobs', function (Blueprint $table) {
            $table->dropColumn(['screening_token', 'screening_pin']);
        });
    }
};
