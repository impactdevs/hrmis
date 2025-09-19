<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('executive_secretary_comments');
            // place it after an existing column, e.g. 'comments'
        });
    }

    public function down()
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }

};
