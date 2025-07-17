<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhistleblowersTable extends Migration
{
    public function up()
    {
        Schema::create('whistleblowers', function (Blueprint $table) {
            $table->uuid('whistleblower_id')->primary();
            $table->string('employee_name');
            $table->string('employee_email');
            $table->string('employee_department');
            $table->string('employee_telephone');
            $table->string('job_title');
            $table->string('submission_type');
            $table->text('description');
            $table->string('individuals_involved')->nullable();
            $table->string('issue_reported')->nullable();
            $table->string('resolution')->nullable();
            $table->string('confidentiality_statement')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('whistleblowers');
    }
}
