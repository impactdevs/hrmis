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
        Schema::table('work_from_homes', function (Blueprint $table) {
            // The edit form and model's $fillable already expect this column
            // — update() has been writing to it since the form was built,
            // it was just never added to the schema.
            $table->string('work_location', 100)->nullable()->after('work_from_home_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_from_homes', function (Blueprint $table) {
            $table->dropColumn('work_location');
        });
    }
};
